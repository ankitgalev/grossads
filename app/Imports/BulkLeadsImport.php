<?php

namespace App\Imports;

use App\Models\Campaign;
use App\Models\CampaignField;
use App\Models\Lead;
use App\Models\LeadValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\AdvertiserLeadDeliveryService;

class BulkLeadsImport implements ToCollection, WithHeadingRow
{
    protected $campaign;
    protected $publisherId;

    public $importedCount = 0;
    public $duplicateCount = 0;
    public $skippedCount = 0;
    public $errors = [];

    public function __construct(Campaign $campaign, $publisherId)
    {
        $this->campaign = $campaign->load('fields');
        $this->publisherId = $publisherId;
    }

    public function collection(Collection $rows)
    {
        $fields = $this->campaign->fields;

        // Only email and phone fields are used for duplicate matching.
        $matchFields = $fields->filter(function ($field) {
            return in_array($field->field_type, ['email', 'phone']);
        })->values();

        if ($matchFields->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'Is campaign mein Email ya Phone field nahi hai. Pehle campaign mein Email ya Phone field add karein.'
            ]);
        }

        foreach ($rows as $index => $row) {

            $rowData = $row instanceof Collection
                ? $row->toArray()
                : (array) $row;

            $normalizedRow = [];

            foreach ($rowData as $key => $value) {
                $normalizedRow[Str::slug($key, '_')] = $value;
            }

            $fieldValues = [];

            foreach ($fields as $field) {

                $fieldKey = Str::slug($field->field_key, '_');
                $fieldName = Str::slug($field->field_name, '_');

                $value = $normalizedRow[$fieldKey]
                    ?? $normalizedRow[$fieldName]
                    ?? null;

                $fieldValues[$field->id] = is_null($value)
                    ? null
                    : trim((string) $value);
            }

            // Identify non-empty email/phone values for duplicate checking.
            $valuesForMatching = [];

            foreach ($matchFields as $field) {
                $value = $fieldValues[$field->id] ?? null;

                if ($this->normalizeValue($value, $field->field_type) !== '') {
                    $valuesForMatching[$field->id] = $value;
                }
            }

            // Reject rows where both email and phone are blank.
            if (empty($valuesForMatching)) {
                $this->skippedCount++;
                $this->errors[] = 'Row ' . ($index + 2) . ': Email aur Phone dono blank hain.';
                continue;
            }

            DB::transaction(function () use (
                $fieldValues,
                $matchFields,
                $valuesForMatching
            ) {

                $duplicate = $this->findDuplicate(
                    $matchFields,
                    $valuesForMatching
                );

                $lead = Lead::create([
                    'campaign_id' => $this->campaign->id,
                    'publisher_id' => $this->publisherId,
                    'advertiser_id' => $this->campaign->advertiser_id,
                    'lead_reference' => 'BULK-' . Str::uuid(),
                    'status' => 'new',
                    'is_duplicate' => $duplicate ? 1 : 0,
                    'duplicate_of_lead_id' => $duplicate ? $duplicate->id : null,
                    'duplicate_remark' => $duplicate
                        ? 'Duplicate found within last 90 days. Original Lead ID: ' . $duplicate->id
                        : null,
                    'source' => 'bulk_import',
                    'submitted_at' => now(),
                ]);

                foreach ($fieldValues as $fieldId => $value) {
                    LeadValue::create([
                        'lead_id' => $lead->id,
                        'campaign_field_id' => $fieldId,
                        'field_value' => $value,
                    ]);
                }

                if ($duplicate) {
                    $this->duplicateCount++;
                } else {
                     // Create advertiser delivery in CRON mode.
                    // This will not send the lead immediately.
                    app(AdvertiserLeadDeliveryService::class)
                        ->createDelivery($lead, true);

                    $this->importedCount++;
                }
            });
        }
    }

    protected function findDuplicate($matchFields, $valuesForMatching)
    {
        $candidates = Lead::where('campaign_id', $this->campaign->id)
            ->where('is_duplicate', 0)
            ->where('created_at', '>=', now()->subDays(90))
            ->with('values')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($candidates as $candidate) {

            $matched = true;

            foreach ($valuesForMatching as $fieldId => $importedValue) {

                $field = $matchFields->firstWhere('id', $fieldId);

                if (!$field) {
                    $matched = false;
                    break;
                }

                $existingValue = $candidate->values
                    ->firstWhere('campaign_field_id', $fieldId);

                $existingValue = $existingValue
                    ? $existingValue->field_value
                    : null;

                $normalizedImported = $this->normalizeValue(
                    $importedValue,
                    $field->field_type
                );

                $normalizedExisting = $this->normalizeValue(
                    $existingValue,
                    $field->field_type
                );

                if (
                    $normalizedImported === '' ||
                    $normalizedExisting === '' ||
                    $normalizedImported !== $normalizedExisting
                ) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                return $candidate;
            }
        }

        return null;
    }

    protected function normalizeValue($value, $type)
    {
        $value = trim((string) ($value ?? ''));

        if ($type === 'email') {
            return strtolower($value);
        }

        if ($type === 'phone') {
            return preg_replace('/\D+/', '', $value);
        }

        return strtolower($value);
    }
}
