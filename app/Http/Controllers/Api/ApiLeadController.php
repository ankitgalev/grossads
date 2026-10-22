<?php
namespace App\Http\Controllers\Api;
//namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use App\Models\CampaignPublisher;
use App\Models\Lead;
use App\Models\LeadValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiLeadController extends Controller
{
    public function store(Request $request)
    {
        // 1. Publisher API key authenticate karein.
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'X-API-KEY header is required.'
            ], 401);
        }

        $assignment = CampaignPublisher::with([
            'campaign.fields'
        ])->where('api_key', $apiKey)
          ->where('status', 1)
          ->first();

        if (!$assignment || !$assignment->campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key or publisher assignment is disabled.'
            ], 401);
        }

        $campaign = $assignment->campaign;

        // 2. Campaign active honi chahiye.
        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This campaign is not active.'
            ], 403);
        }

        // 3. Daily aur total lead limits check karein.
        $today = Carbon::today();

        $leadQuery = Lead::where('campaign_id', $campaign->id)
            ->where('publisher_id', $assignment->publisher_id);

        $totalLeads = (clone $leadQuery)->count();

        $todayLeads = (clone $leadQuery)
            ->where('created_at', '>=', $today)
            ->where('created_at', '<', $today->copy()->addDay())
            ->count();

        if (
            (int) $assignment->daily_limit > 0 &&
            $todayLeads >= (int) $assignment->daily_limit
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Daily lead limit reached.'
            ], 429);
        }

        if (
            (int) $assignment->total_limit > 0 &&
            $totalLeads >= (int) $assignment->total_limit
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Total lead limit reached.'
            ], 429);
        }

        // 4. Lead data "data" object ke andar bhejna hoga.
        $input = $request->input('data');

        if (!is_array($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide lead fields inside the data object.'
            ], 422);
        }

        $fields = $campaign->fields;

        if ($fields->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No dynamic fields configured for this campaign.'
            ], 422);
        }

        // 5. Sirf campaign ke configured field keys accept karein.
        $allowedKeys = $fields->pluck('field_key')->all();

        $unknownKeys = array_diff(array_keys($input), $allowedKeys);

        if (!empty($unknownKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown field keys supplied.',
                'fields' => array_values($unknownKeys)
            ], 422);
        }

        // 6. Dynamic validation rules banayein.
        $rules = [];
        $messages = [];

        foreach ($fields as $field) {
            $key = $field->field_key;

            $rules['data.' . $key] = $field->is_required
                ? 'required'
                : 'nullable';

            switch ($field->field_type) {
                case 'email':
                    $rules['data.' . $key] .= '|email';
                    break;

                case 'phone':
                    $rules['data.' . $key] .= '|string|max:30';
                    break;

                case 'number':
                    $rules['data.' . $key] .= '|numeric';
                    break;

                case 'date':
                    $rules['data.' . $key] .= '|date';
                    break;

                case 'select':
                    $rules['data.' . $key] .= '|string';

                    $options = $field->options;

                    if (is_string($options)) {
                        $decoded = json_decode($options, true);
                        $options = is_array($decoded) ? $decoded : [];
                    }

                    if (is_array($options) && count($options) > 0) {
                        $rules['data.' . $key] .= '|in:' .
                            implode(',', array_map('strval', $options));
                    }
                    break;

                case 'text':
                case 'textarea':
                    $rules['data.' . $key] .= '|string|max:5000';
                    break;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lead validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 7. Lead aur field values ek transaction mein save karein.
        try {
            $lead = DB::transaction(function () use (
                $request,
                $assignment,
                $campaign,
                $fields,
                $input
            ) {
                $reference = 'GA' . strtoupper(Str::random(14));

                $lead = Lead::create([
                    'campaign_id' => $campaign->id,
                    'publisher_id' => $assignment->publisher_id,
                    'advertiser_id' => $campaign->advertiser_id,
                    'lead_reference' => $reference,
                    'status' => 'new',
                    'source' => 'api',
                    'ip_address' => $request->ip(),
                    'user_agent' => substr(
                        (string) $request->userAgent(),
                        0,
                        1000
                    ),
                    'submitted_at' => now()
                ]);

                foreach ($fields as $field) {
                    $key = $field->field_key;

                    if (
                        !array_key_exists($key, $input) ||
                        $input[$key] === null
                    ) {
                        continue;
                    }

                    $value = $input[$key];

                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value);
                    }

                    LeadValue::create([
                        'lead_id' => $lead->id,
                        'campaign_field_id' => $field->id,
                        'field_value' => (string) $value
                    ]);
                }

                return $lead;
            });

            return response()->json([
                'success' => true,
                'message' => 'Lead submitted successfully.',
                'lead_reference' => $lead->lead_reference,
                'lead_id' => $lead->id,
                'status' => $lead->status
            ], 201);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Lead could not be saved. Please check the server logs.'
            ], 500);
        }
    }
}
