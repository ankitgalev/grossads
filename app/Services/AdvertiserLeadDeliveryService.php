<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\AdvertiserLeadDelivery;
use App\Models\AdvertiserApiLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Throwable;

class AdvertiserLeadDeliveryService
{
    /**
     * Create one delivery record per lead.
     */
    public function createDelivery(Lead $lead, $forceCron = false)
    {
        // Never create advertiser delivery for duplicate leads.
        if ($lead->is_duplicate) {
            return null;
        }
        $lead->loadMissing(['campaign.fields', 'values.field']);

        $campaign = $lead->campaign;

        if (!$campaign || !$campaign->advertiser_delivery_enabled) {
            return null;
        }

        if (empty($campaign->advertiser_api_url)) {
            Log::warning('Advertiser API URL missing.', [
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id
            ]);

            return null;
        }

        // Only send when lead status is new and campaign setting is new.
        if (!$this->shouldForward($campaign, $lead)) {
            return null;
        }

        $payload = $this->buildPayload($lead, $campaign);

        try {
            $delivery = AdvertiserLeadDelivery::firstOrCreate(
                ['lead_id' => $lead->id],
                [
                    'campaign_id' => $campaign->id,
                    'advertiser_id' => $campaign->advertiser_id,
                    //'delivery_mode' => $campaign->advertiser_delivery_mode,
                    'delivery_mode' => $forceCron    ? 'cron'  : $campaign->advertiser_delivery_mode,
                    'trigger_status' => $lead->status,
                    'status' => 'pending',
                    'request_payload' => $payload,
                    'attempt_count' => 0,
                    'max_attempts' => max(1, (int) $campaign->advertiser_retry_attempts),
                    'next_attempt_at' => now()
                ]
            );
        } catch (QueryException $e) {
            // Another request may have created this lead's delivery first.
            $delivery = AdvertiserLeadDelivery::where('lead_id', $lead->id)->first();

            if (!$delivery) {
                throw $e;
            }
        }

        // Send immediately only when this is a newly created real-time delivery.
        if (
        //    $delivery->wasRecentlyCreated &&
        //    $campaign->advertiser_delivery_mode === 'real_time'
        $delivery->wasRecentlyCreated && $delivery->delivery_mode === 'real_time'
        ) {
            $this->sendDelivery($delivery);
        }

        return $delivery;
    }

    /**
     * Only new leads are eligible.
     */
    protected function shouldForward(Campaign $campaign, Lead $lead)
    {
        return $lead->status === 'new'
            && $campaign->advertiser_delivery_status === 'new';
    }

    /**
     * Build advertiser API payload using campaign field mapping.
     */
    protected function buildPayload(Lead $lead, Campaign $campaign)
    {
        $mapping = $campaign->advertiser_field_mapping ?: array();

        $payload = array();

        foreach ($lead->values as $value) {
            $field = $value->field;

            if (!$field) {
                continue;
            }

            $sourceKey = $field->field_key;

            $destinationKey = isset($mapping[$sourceKey])
                ? $mapping[$sourceKey]
                : $sourceKey;

            if ($destinationKey === null || $destinationKey === '') {
                continue;
            }

            $payload[$destinationKey] = $value->field_value;
        }

        $payload['lead_reference'] = $lead->lead_reference;
        $payload['campaign_id'] = $lead->campaign_id;

        return $payload;
    }

    /**
     * Send one delivery.
     * A database lock prevents two workers from sending it simultaneously.
     */
    public function sendDelivery(AdvertiserLeadDelivery $delivery)
    {
        $deliveryId = $delivery->id;

        $reserved = DB::transaction(function () use ($deliveryId) {

            $row = AdvertiserLeadDelivery::where('id', $deliveryId)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                return null;
            }

            // Never send an already successful or currently processing delivery.
            if (in_array($row->status, array('success', 'processing', 'failed'))) {
                return null;
            }

            // Respect retry schedule.
            if ($row->next_attempt_at && $row->next_attempt_at->gt(now())) {
                return null;
            }

            if ($row->attempt_count >= $row->max_attempts) {
                $row->update([
                    'status' => 'failed',
                    'locked_at' => null,
                    'last_error' => 'Maximum retry attempts reached.'
                ]);

                return null;
            }

            $attemptNumber = $row->attempt_count + 1;

            $row->update([
                'status' => 'processing',
                'locked_at' => now(),
                'attempt_count' => $attemptNumber,
                'last_attempt_at' => now()
            ]);

            return array(
                'delivery' => $row,
                'attempt_number' => $attemptNumber
            );
        });

        if (!$reserved) {
            return false;
        }

        $delivery = $reserved['delivery'];
        $attemptNumber = $reserved['attempt_number'];

        $delivery->refresh();
        $delivery->load('campaign');

        $campaign = $delivery->campaign;

        if (!$campaign || empty($campaign->advertiser_api_url)) {
            $this->saveErrorLog(
                $delivery,
                $attemptNumber,
                'Campaign API URL is missing.'
            );

            return $this->markFailure(
                $delivery,
                'Campaign API URL is missing.'
            );
        }

        $url = $campaign->advertiser_api_url;
        $method = strtoupper($campaign->advertiser_http_method);
        $format = $campaign->advertiser_payload_format;
        $payload = $delivery->request_payload ?: array();

        try {
            $http = Http::timeout(
                max(1, (int) $campaign->advertiser_timeout)
            );

            $http = $this->applyAuthentication($http, $campaign);

            if ($format === 'form') {
                $http = $http->asForm();

                $response = $http->send($method, $url, array(
                    'form_params' => $payload
                ));

            } elseif ($format === 'query') {

                $response = $http->send($method, $url, array(
                    'query' => $payload
                ));

            } else {

                $response = $http->send($method, $url, array(
                    'json' => $payload
                ));
            }

            $responseBody = substr($response->body(), 0, 50000);
            $success = $response->successful();

            AdvertiserApiLog::create([
                'delivery_id' => $delivery->id,
                'attempt_number' => $attemptNumber,
                'request_url' => $url,
                'http_method' => $method,
                'request_payload' => $payload,
                'http_status' => $response->status(),
                'response_body' => $responseBody,
                'is_success' => $success,
                'error_message' => $success ? null : 'HTTP error: ' . $response->status(),
                'attempted_at' => now()
            ]);

            if ($success) {
                $delivery->update([
                    'status' => 'success',
                    'last_http_status' => $response->status(),
                    'last_response' => $responseBody,
                    'last_error' => null,
                    'delivered_at' => now(),
                    'locked_at' => null,
                    'next_attempt_at' => null
                ]);

                return true;
            }

            return $this->markFailure(
                $delivery,
                'HTTP error: ' . $response->status(),
                $response->status(),
                $responseBody
            );

        } catch (Throwable $e) {

            Log::error('Advertiser API delivery exception.', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage()
            ]);

            $this->saveErrorLog(
                $delivery,
                $attemptNumber,
                $e->getMessage(),
                $url,
                $method,
                $payload
            );

            return $this->markFailure(
                $delivery,
                $e->getMessage()
            );
        }
    }

    /**
     * Apply advertiser API authentication.
     */
    protected function applyAuthentication($http, Campaign $campaign)
    {
        $config = $campaign->advertiser_auth_config ?: array();

        switch ($campaign->advertiser_auth_type) {

            case 'bearer':

                if (!empty($config['token'])) {
                    $http = $http->withToken($config['token']);
                }

                break;

            case 'api_key':

                $header = !empty($config['header'])
                    ? $config['header']
                    : 'X-API-KEY';

                $value = isset($config['value'])
                    ? $config['value']
                    : (isset($config['key']) ? $config['key'] : '');

                if ($value !== '') {
                    $http = $http->withHeaders(array(
                        $header => $value
                    ));
                }

                break;

            case 'basic':

                if (isset($config['username'])) {
                    $http = $http->withBasicAuth(
                        $config['username'],
                        isset($config['password']) ? $config['password'] : ''
                    );
                }

                break;

            case 'custom_headers':

                if (!empty($config['headers']) && is_array($config['headers'])) {
                    $http = $http->withHeaders($config['headers']);
                }

                break;
        }

        return $http;
    }

    /**
     * Save exception log.
     */
    protected function saveErrorLog(
        AdvertiserLeadDelivery $delivery,
        $attemptNumber,
        $error,
        $url = null,
        $method = null,
        $payload = array()
    ) {
        AdvertiserApiLog::create([
            'delivery_id' => $delivery->id,
            'attempt_number' => $attemptNumber,
            'request_url' => $url,
            'http_method' => $method,
            'request_payload' => $payload,
            'http_status' => null,
            'response_body' => null,
            'is_success' => false,
            'error_message' => substr($error, 0, 5000),
            'attempted_at' => now()
        ]);
    }

    /**
     * Mark failed attempt and schedule retry if available.
     */
    protected function markFailure(
        AdvertiserLeadDelivery $delivery,
        $error,
        $httpStatus = null,
        $responseBody = null
    ) {
        $delivery->refresh();

        $attempts = (int) $delivery->attempt_count;
        $maxAttempts = (int) $delivery->max_attempts;

        $canRetry = $attempts < $maxAttempts;

        // Retry delay: 5, 10, 15... minutes, maximum 60 minutes.
        $nextAttempt = $canRetry
            ? now()->addMinutes(min(60, 5 * $attempts))
            : null;

        $delivery->update([
            'status' => $canRetry ? 'pending' : 'failed',
            'last_http_status' => $httpStatus,
            'last_response' => $responseBody
                ? substr($responseBody, 0, 50000)
                : null,
            'last_error' => substr($error, 0, 5000),
            'next_attempt_at' => $nextAttempt,
            'locked_at' => null
        ]);

        return false;
    }
}