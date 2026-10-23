<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MetaIntegration;
use App\Models\MetaLeadLog;
use App\Models\Lead;
use App\Models\LeadValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class MetaLeadWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request);
        }

        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        if (!is_array($payload) || !isset($payload['entry'])) {
            Log::warning('【entity-Meta¦canonical_name=Meta】 webhook: Invalid payload', ['body' => substr($rawBody, 0, 500)]);
            return response('Invalid payload', 400);
        }

        $hasFailure = false;

        foreach ($payload['entry'] as $entry) {
            $pageId = isset($entry['id']) ? (string) $entry['id'] : '';
            if (empty($entry['changes']) || !is_array($entry['changes'])) continue;

            foreach ($entry['changes'] as $change) {
                if (!isset($change['field']) || $change['field'] !== 'leadgen') continue;

                $value = isset($change['value']) && is_array($change['value']) ? $change['value'] : [];
                $leadId = isset($value['leadgen_id']) ? (string) $value['leadgen_id'] : '';
                $formId = isset($value['form_id']) ? (string) $value['form_id'] : '';
                $eventPageId = !empty($value['page_id']) ? (string) $value['page_id'] : $pageId;

                if ($leadId === '' || $eventPageId === '') {
                    Log::warning('【entity-Meta¦canonical_name=Meta】 webhook: missing leadgen_id or page_id.');
                    continue;
                }

                $integration = $this->findIntegration($eventPageId, $formId);

                if (!$integration) {
                    Log::warning('【entity-Meta¦canonical_name=Meta】 webhook: no active matching integration.', [
                        'page_id' => $eventPageId, 'form_id' => $formId, 'leadgen_id' => $leadId,
                    ]);
                    continue;
                }

                // Signature validation - only if app_secret is present after decryption
                $decryptedSecret = $integration->app_secret; // Model accessor se decrypt hoke ayega
                if ($decryptedSecret !== '') {
                    $signature = (string) $request->header('X-Hub-Signature-256', '');
                    $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $decryptedSecret);

                    if ($signature === '' || !hash_equals($expected, $signature)) {
                        Log::warning('【entity-Meta¦canonical_name=Meta】 webhook signature validation failed.', ['page_id' => $eventPageId]);
                        return response('Invalid signature', 403);
                    }
                }

                try {
                    $this->processLead($integration, $leadId, $eventPageId, $formId);
                } catch (Throwable $e) {
                    $hasFailure = true;
                    Log::error('【entity-Meta¦canonical_name=Meta】 lead processing failed.', [
                        'leadgen_id' => $leadId, 'page_id' => $eventPageId, 'error' => $e->getMessage(),
                    ]);
                    $log = MetaLeadLog::where('meta_lead_id', $leadId)->first();
                    if ($log) {
                        $log->status = 'failed';
                        $log->error_message = substr($e->getMessage(), 0, 60000);
                        $log->save();
                    }
                }
            }
        }

        if ($hasFailure) {
            return response('Lead processing failed; retry delivery.', 500);
        }
        return response('EVENT_RECEIVED', 200);
    }

    private function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode') ?? $request->input('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token') ?? $request->input('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge') ?? $request->input('hub.challenge');

        if ($mode !== 'subscribe' || $token === null || $challenge === null) {
            return response('Forbidden', 403);
        }

        // 1. Hardcoded + ENV fallback - aapki requirement
        $envToken = env('META_VERIFY_TOKEN', 'GrossAdsLeadIntegration0099774567');
        if (hash_equals($envToken, (string) $token)) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        // 2. DB check - Model accessor se decrypt hoke ayega
        try {
            $integrations = MetaIntegration::all();
            foreach ($integrations as $integration) {
                $decryptedVerify = $integration->verify_token;
                if ($decryptedVerify !== '' && hash_equals($decryptedVerify, (string) $token)) {
                    return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
                }
            }
        } catch (Throwable $e) {
            Log::error('verifyWebhook DB error: '.$e->getMessage());
        }
        return response('Forbidden', 403);
    }

    // private function findIntegration($pageId, $formId)
    // {
    //     $integrations = MetaIntegration::where('page_id', $pageId)->where('status', true)->get();

    //     // Exact Form ID match
    //     foreach ($integrations as $integration) {
    //         if ($formId !== '' && $integration->form_id !== null && (string) $integration->form_id === (string) $formId) {
    //             return $integration;
    //         }
    //     }
    //     // Accept all forms (form_id = NULL)
    //     foreach ($integrations as $integration) {
    //         if (empty($integration->form_id)) {
    //             return $integration;
    //         }
    //     }
    //     return null;
    // }
    private function findIntegration(?string $pageId, ?string $formId): ?MetaIntegration
    {
        if (empty($pageId)) return null;

        // TEST MODE: Agar Meta 44444 bhejta hai to pehla active integration le lo
        if ($pageId === '444444444444' || $formId === '444444444444') {
            return MetaIntegration::where('status', true)->whereNotNull('page_id')->first();
        }

        // Normal logic
        $base = MetaIntegration::where('page_id', $pageId)->where('status', true);
        if ($formId !== null) {
            $exact = (clone $base)->where('form_id', $formId)->first();
            if ($exact) return $exact;
            $anyForm = (clone $base)->whereNull('form_id')->first();
            if ($anyForm) return $anyForm;
        }
        return $base->whereNull('form_id')->first();
    }

    private function processLead(MetaIntegration $integration, $metaLeadId, $pageId, $formId)
    {
        // --- TEST MODE: 44444 ke liye fake lead banao ---
    if ($leadId === '444444444444' || $pageId === '444444444444') {
        $log = MetaLeadLog::create([
            'meta_lead_id' => $leadId,
            '【entity-meta¦canonical_name=Meta】_integration_id' => $integration->id,
            'form_id' => $formId,
            'page_id' => $pageId,
            'status' => 'processed',
            'error_message' => null,
            'raw_payload' => json_encode(['test' => true]),
        ]);

        // Fake lead entry in leads table
        \App\Models\Lead::create([
            'meta_lead_id' => $leadId,
            'meta_integration_id' => $integration->id,
            'form_id' => $formId ?? 'test_form',
            'page_id' => $pageId,
            'campaign_id' => $integration->campaign_id,
            'publisher_id' => $integration->publisher_id,
            'full_name' => 'Test User (Webhook)',
            'email' => 'test@webhook.com',
            'phone' => '9999999999',
            'raw_data' => json_encode(['test' => true, 'note' => 'This is dummy webhook test']),
            'status' => 'new',
        ]);

        Log::info('Test lead created successfully', ['lead_id' => $leadId]);
        return;
    }
    // --- TEST MODE END ---
        $existingLog = MetaLeadLog::where('meta_lead_id', $metaLeadId)->first();
        if ($existingLog && $existingLog->status === 'processed') return;

        if (!$existingLog) {
            $existingLog = new MetaLeadLog();
            $existingLog->integration_id = $integration->id;
            $existingLog->meta_lead_id = $metaLeadId;
        }
        $existingLog->page_id = $pageId;
        $existingLog->form_id = $formId ?: null;
        $existingLog->status = 'processing';
        $existingLog->error_message = null;
        $existingLog->save();

        try {
            $version = env('META_GRAPH_VERSION', 'v20.0'); // v26 ki jagah v20 stable
            $version = trim($version, '/');

            // --- AAPKI REQUIREMENT: Never Expired Token Handling ---
            // Model se decrypt hoke page_access_token ayega
            $pageToken = $integration->page_access_token; 
            // Agar page token me aapne System User Token (EAATm...) dala hai to bhi chalega
            // Fallback ke liye .env se System Token
            $systemToken = env('【entity-META_SYSTEM_USER_TOKEN¦canonical_name=Meta】', '');

            $tokensToTry = array_filter([$pageToken, $systemToken]); // Khali token hata do

            if (empty($tokensToTry)) {
                throw new \RuntimeException('No valid Page or System User token found in integration or .env');
            }

            $response = null;
            $lastError = '';
            foreach ($tokensToTry as $token) {
                $response = Http::timeout(25)->get(
                    'https://graph.facebook.com/' . $version . '/' . $metaLeadId,
                    [
                        'fields' => 'id,created_time,form_id,field_data,ad_id',
                        'access_token' => $token,
                    ]
                );
                if ($response->successful()) break;
                $lastError = $response->body();
            }

            if (!$response || !$response->successful()) {
                throw new \RuntimeException('【entity-Meta Graph API¦canonical_name=Meta】 failed: ' . $lastError);
            }

            $metaData = $response->json();
            if (!is_array($metaData) || empty($metaData['id'])) {
                throw new \RuntimeException('【entity-Meta¦canonical_name=Meta】 API response did not contain a lead ID.');
            }

            $campaign = $integration->campaign;
            if (!$campaign) throw new \RuntimeException('Configured GrossAds campaign no longer exists.');

            $fieldData = [];
            foreach ((array) ($metaData['field_data'] ?? []) as $item) {
                if (!isset($item['name'])) continue;
                $fieldName = $this->normaliseKey($item['name']);
                $values = $item['values'] ?? [];
                $fieldData[$fieldName] = is_array($values) ? implode(', ', array_map('strval', $values)) : (string) $values;
            }

            DB::transaction(function () use ($integration, $metaLeadId, $metaData, $fieldData, $campaign, $existingLog) {
                $lead = Lead::where('lead_reference', '【entity-META¦canonical_name=Meta】-' . $metaLeadId)->first();
                if (!$lead) {
                    $lead = new Lead();
                    $lead->campaign_id = $campaign->id;
                    $lead->publisher_id = $integration->publisher_id;
                    $lead->advertiser_id = $campaign->advertiser_id;
                    $lead->lead_reference = 'META-' . $metaLeadId;
                    $lead->status = 'new';
                    $lead->source = 'meta_lead_ads';
                    $lead->user_agent = 'Meta Lead Ads Webhook';
                    $lead->submitted_at = !empty($metaData['created_time']) ? Carbon::parse($metaData['created_time']) : Carbon::now();
                    $lead->save();
                }

                $fields = $campaign->fields()->get();
                foreach ($fields as $campaignField) {
                    $candidateKeys = [
                        $this->normaliseKey($campaignField->field_key),
                        $this->normaliseKey($campaignField->field_name),
                    ];
                    $aliases = [
                        'name' => ['fullname', 'full_name', 'name'], 'fullname' => ['fullname', 'full_name', 'name'],
                        'email' => ['email', 'emailaddress'], 'phone' => ['phone', 'phonenumber', 'mobile', 'mobilephone'],
                        'mobile' => ['mobile', 'phonenumber', 'phone', 'mobilephone'],
                    ];
                    foreach ($candidateKeys as $ck) { if (isset($aliases[$ck])) $candidateKeys = array_merge($candidateKeys, $aliases[$ck]); }
                    $candidateKeys = array_unique($candidateKeys);
                    $matchedValue = null; $found = false;
                    foreach ($candidateKeys as $key) {
                        if ($key !== '' && array_key_exists($key, $fieldData)) { $matchedValue = $fieldData[$key]; $found = true; break; }
                    }
                    if (!$found) continue;
                    $leadValue = LeadValue::firstOrNew(['lead_id' => $lead->id, 'campaign_field_id' => $campaignField->id]);
                    $leadValue->field_value = $matchedValue;
                    $leadValue->save();
                }
                $existingLog->lead_id = $lead->id;
                $existingLog->status = 'processed';
                $existingLog->error_message = null;
                $existingLog->save();
            });
        } catch (Throwable $e) {
            $existingLog->status = 'failed';
            $existingLog->error_message = substr($e->getMessage(), 0, 60000);
            $existingLog->save();
            throw $e;
        }
    }

    private function normaliseKey($value)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $value));
    }
}
// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use App\Models\MetaIntegration;
// use App\Models\MetaLeadLog;
// use App\Models\Lead;
// use App\Models\LeadValue;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str;
// use Carbon\Carbon;
// use Throwable;

// class MetaLeadWebhookController extends Controller
// {
//     public function handle(Request $request)
//     {
//         // Meta's initial webhook verification handshake.
//         if ($request->isMethod('get')) {
//             return $this->verifyWebhook($request);
//         }

//         $rawBody = $request->getContent();
//         $payload = json_decode($rawBody, true);

//         if (!is_array($payload) || !isset($payload['entry'])) {
//             return response('Invalid payload', 400);
//         }

//         $hasFailure = false;

//         foreach ($payload['entry'] as $entry) {
//             $pageId = isset($entry['id']) ? (string) $entry['id'] : '';

//             if (empty($entry['changes']) || !is_array($entry['changes'])) {
//                 continue;
//             }

//             foreach ($entry['changes'] as $change) {
//                 if (!isset($change['field']) || $change['field'] !== 'leadgen') {
//                     continue;
//                 }

//                 $value = isset($change['value']) && is_array($change['value'])
//                     ? $change['value'] : [];

//                 $leadId = isset($value['leadgen_id'])
//                     ? (string) $value['leadgen_id'] : '';

//                 $formId = isset($value['form_id'])
//                     ? (string) $value['form_id'] : '';

//                 $eventPageId = !empty($value['page_id'])
//                     ? (string) $value['page_id'] : $pageId;

//                 if ($leadId === '' || $eventPageId === '') {
//                     Log::warning('Meta webhook: missing leadgen_id or page_id.');
//                     continue;
//                 }

//                 $integration = $this->findIntegration($eventPageId, $formId);

//                 // Unknown, disabled, or unmatched Page/Form.
//                 if (!$integration) {
//                     Log::warning('Meta webhook: no active matching integration.', [
//                         'page_id' => $eventPageId,
//                         'form_id' => $formId,
//                         'leadgen_id' => $leadId,
//                     ]);
//                     continue;
//                 }

//                 // Validate the raw, unmodified request body with the App Secret.
//                 $signature = (string) $request->header('X-Hub-Signature-256', '');
//                 $expected = 'sha256=' . hash_hmac(
//                     'sha256',
//                     $rawBody,
//                     $integration->app_secret
//                 );

//                 // if ($integration->app_secret === '' ||
//                 //     $signature === '' ||
//                 //     !hash_equals($expected, $signature)) {
//                 //     Log::warning('Meta webhook signature validation failed.', [
//                 //         'page_id' => $eventPageId,
//                 //     ]);

//                 //     return response('Invalid signature', 403);
//                 // }
//                 // Validate only if app_secret is configured
//                 if ($integration->app_secret !== '') {
//                     $signature = (string) $request->header('X-Hub-Signature-256', '');
//                     $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $integration->app_secret);

//                     if ($signature === '' || !hash_equals($expected, $signature)) {
//                         Log::warning('Meta webhook signature validation failed.', [
//                             'page_id' => $eventPageId,
//                         ]);
//                         return response('Invalid signature', 403);
//                     }
//                 }

//                 try {
//                     $this->processLead(
//                         $integration,
//                         $leadId,
//                         $eventPageId,
//                         $formId
//                     );
//                 } catch (Throwable $e) {
//                     $hasFailure = true;

//                     Log::error('Meta lead processing failed.', [
//                         'leadgen_id' => $leadId,
//                         'page_id' => $eventPageId,
//                         'error' => $e->getMessage(),
//                     ]);

//                     $log = MetaLeadLog::where('meta_lead_id', $leadId)->first();

//                     if ($log) {
//                         $log->status = 'failed';
//                         $log->error_message = substr($e->getMessage(), 0, 60000);
//                         $log->save();
//                     }
//                 }
//             }
//         }

//         // A non-200 response allows Meta to retry failed deliveries.
//         if ($hasFailure) {
//             return response('Lead processing failed; retry delivery.', 500);
//         }

//         return response('EVENT_RECEIVED', 200);
//     }

//     // private function verifyWebhook(Request $request)
//     // {
//     //     $mode = $request->query('hub_mode', $request->query('hub.mode'));
//     //     $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
//     //     $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

//     //     if ($mode !== 'subscribe' || $token === null || $challenge === null) {
//     //         return response('Forbidden', 403);
//     //     }

//     //     // Compare against configured integrations; token is encrypted at rest.
//     //     $integrations = MetaIntegration::all();

//     //     foreach ($integrations as $integration) {
//     //         if ($integration->verify_token !== '' &&
//     //             hash_equals((string) $integration->verify_token, (string) $token)) {
//     //             return response((string) $challenge, 200)
//     //                 ->header('Content-Type', 'text/plain');
//     //         }
//     //     }

//     //     return response('Forbidden', 403);
//     // }
//     private function verifyWebhook(Request $request)
//     {
//         $mode = $request->query('hub_mode') ?? $request->query('hub.mode') ?? $request->input('hub.mode');
//         $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token') ?? $request->input('hub.verify_token');
//         $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge') ?? $request->input('hub.challenge');

//         if ($mode !== 'subscribe' || $token === null || $challenge === null) {
//             return response('Forbidden', 403);
//         }

//         // 1. HARDCODED fallback - lets you verify even when DB is empty
//         if (hash_equals('GrossAdsLeadIntegration0099774567', (string) $token)) {
//             return response((string) $challenge, 200)
//                 ->header('Content-Type', 'text/plain');
//         }

//         // 2. Then check DB
//         try {
//             $integrations = MetaIntegration::all();
//             foreach ($integrations as $integration) {
//                 if ($integration->verify_token !== '' &&
//                     hash_equals((string) $integration->verify_token, (string) $token)) {
//                     return response((string) $challenge, 200)
//                         ->header('Content-Type', 'text/plain');
//                 }
//             }
//         } catch (\Throwable $e) {
//             Log::error('verifyWebhook DB error: '.$e->getMessage());
//             // don't throw, still return 403 so Meta gets a proper response
//         }

//         return response('Forbidden', 403);
//     }

//     private function findIntegration($pageId, $formId)
//     {
//         $integrations = MetaIntegration::where('page_id', $pageId)
//             ->where('status', true)
//             ->get();

//         // Prefer an exact Form ID match.
//         foreach ($integrations as $integration) {
//             if ($formId !== '' &&
//                 $integration->form_id !== null &&
//                 (string) $integration->form_id === (string) $formId) {
//                 return $integration;
//             }
//         }

//         // An empty configured Form ID means "accept all forms for this Page".
//         foreach ($integrations as $integration) {
//             if (empty($integration->form_id)) {
//                 return $integration;
//             }
//         }

//         return null;
//     }

//     private function processLead(
//         MetaIntegration $integration,
//         $metaLeadId,
//         $pageId,
//         $formId
//     ) {
//         $existingLog = MetaLeadLog::where('meta_lead_id', $metaLeadId)->first();

//         if ($existingLog && $existingLog->status === 'processed') {
//             return; // Duplicate webhook delivery: already saved.
//         }

//         if (!$existingLog) {
//             $existingLog = new MetaLeadLog();
//             $existingLog->integration_id = $integration->id;
//             $existingLog->meta_lead_id = $metaLeadId;
//         }

//         $existingLog->page_id = $pageId;
//         $existingLog->form_id = $formId ?: null;
//         $existingLog->status = 'processing';
//         $existingLog->error_message = null;
//         $existingLog->save();

//         try {
//             $version = env('META_GRAPH_VERSION', 'v26.0');
//             $version = trim($version, '/');

//             $response = Http::timeout(25)->get(
//                 'https://graph.facebook.com/' . $version . '/' . $metaLeadId,
//                 [
//                     'fields' => 'id,created_time,form_id,field_data,ad_id',
//                     'access_token' => $integration->page_access_token,
//                 ]
//             );

//             if (!$response->successful()) {
//                 throw new \RuntimeException(
//                     'Meta Graph API returned HTTP ' . $response->status() .
//                     ': ' . $response->body()
//                 );
//             }

//             $metaData = $response->json();

//             if (!is_array($metaData) || empty($metaData['id'])) {
//                 throw new \RuntimeException('Meta API response did not contain a lead ID.');
//             }

//             $campaign = $integration->campaign;

//             if (!$campaign) {
//                 throw new \RuntimeException('Configured GrossAds campaign no longer exists.');
//             }

//             $fieldData = [];
//             foreach ((array) (isset($metaData['field_data']) ? $metaData['field_data'] : []) as $item) {
//                 if (!isset($item['name'])) {
//                     continue;
//                 }

//                 $fieldName = $this->normaliseKey($item['name']);
//                 $values = isset($item['values']) ? $item['values'] : [];
//                 $fieldData[$fieldName] = is_array($values)
//                     ? implode(', ', array_map('strval', $values))
//                     : (string) $values;
//             }

//             DB::transaction(function () use (
//                 $integration,
//                 $metaLeadId,
//                 $metaData,
//                 $fieldData,
//                 $campaign,
//                 $existingLog
//             ) {
//                 // A prior failed attempt may already have created the GrossAds lead.
//                 $lead = Lead::where('lead_reference', 'META-' . $metaLeadId)->first();

//                 if (!$lead) {
//                     $lead = new Lead();
//                     $lead->campaign_id = $campaign->id;
//                     $lead->publisher_id = $integration->publisher_id;
//                     $lead->advertiser_id = $campaign->advertiser_id;
//                     $lead->lead_reference = 'META-' . $metaLeadId;
//                     $lead->status = 'new';
//                     $lead->source = 'meta_lead_ads';
//                     $lead->ip_address = null;
//                     $lead->user_agent = 'Meta Lead Ads Webhook';

//                     if (!empty($metaData['created_time'])) {
//                         $lead->submitted_at = Carbon::parse($metaData['created_time']);
//                     } else {
//                         $lead->submitted_at = Carbon::now();
//                     }

//                     $lead->save();
//                 }

//                 // Match Meta's field names to the dynamic fields configured in GrossAds.
//                 $fields = $campaign->fields()->get();

//                 foreach ($fields as $campaignField) {
//                     $candidateKeys = [
//                         $this->normaliseKey($campaignField->field_key),
//                         $this->normaliseKey($campaignField->field_name),
//                     ];

//                     // Common Meta/GrossAds field name differences.
//                     $aliases = [
//                         'name' => ['fullname', 'full_name', 'name'],
//                         'fullname' => ['fullname', 'full_name', 'name'],
//                         'email' => ['email', 'emailaddress'],
//                         'phone' => ['phone', 'phonenumber', 'mobile', 'mobilephone'],
//                         'mobile' => ['mobile', 'phonenumber', 'phone', 'mobilephone'],
//                         'city' => ['city', 'town'],
//                         'state' => ['state', 'province'],
//                         'zip' => ['zip', 'zipcode', 'postalcode', 'postcode'],
//                         'country' => ['country'],
//                     ];

//                     foreach ($candidateKeys as $candidateKey) {
//                         if (isset($aliases[$candidateKey])) {
//                             $candidateKeys = array_merge(
//                                 $candidateKeys,
//                                 $aliases[$candidateKey]
//                             );
//                         }
//                     }

//                     $candidateKeys = array_unique($candidateKeys);
//                     $matchedValue = null;
//                     $found = false;

//                     foreach ($candidateKeys as $key) {
//                         if ($key !== '' && array_key_exists($key, $fieldData)) {
//                             $matchedValue = $fieldData[$key];
//                             $found = true;
//                             break;
//                         }
//                     }

//                     if (!$found) {
//                         continue;
//                     }

//                     $leadValue = LeadValue::firstOrNew([
//                         'lead_id' => $lead->id,
//                         'campaign_field_id' => $campaignField->id,
//                     ]);

//                     $leadValue->field_value = $matchedValue;
//                     $leadValue->save();
//                 }

//                 $existingLog->lead_id = $lead->id;
//                 $existingLog->status = 'processed';
//                 $existingLog->error_message = null;
//                 $existingLog->save();
//             });
//         } catch (Throwable $e) {
//             $existingLog->status = 'failed';
//             $existingLog->error_message = substr($e->getMessage(), 0, 60000);
//             $existingLog->save();

//             throw $e;
//         }
//     }

//     private function normaliseKey($value)
//     {
//         return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $value));
//     }
// }
