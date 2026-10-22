<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\LeadValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadApiController extends Controller
{
    public function store(Request $request)
    {
        $apiKey = $request->header('X-API-KEY')
            ?? $request->input('api_key');


        if (!$apiKey) {

            return response()->json([
                'success' => false,
                'message' => 'API key is required.'
            ], 401);
        }


        $campaign = Campaign::with('fields')
            ->where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();


        if (!$campaign) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive campaign API key.'
            ], 401);
        }


        $rules = [];


        foreach ($campaign->fields as $field) {

            $rule = [];


            if ($field->is_required) {

                $rule[] = 'required';

            } else {

                $rule[] = 'nullable';
            }


            switch ($field->field_type) {

                case 'email':

                    $rule[] = 'email';

                    break;


                case 'phone':

                    $rule[] = 'string';
                    $rule[] = 'min:7';
                    $rule[] = 'max:20';

                    break;


                case 'number':

                    $rule[] = 'numeric';

                    break;


                case 'date':

                    $rule[] = 'date';

                    break;


                default:

                    $rule[] = 'string';
            }


            $rules[$field->field_key] = $rule;
        }


        $validator = validator(
            $request->all(),
            $rules
        );


        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validation failed.',

                'errors' =>
                    $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Lead Check
        |--------------------------------------------------------------------------
        */

        $mobileField = $campaign->fields
            ->first(function ($field) {

                return in_array(
                    strtolower($field->field_key),
                    [
                        'mobile',
                        'phone',
                        'mobile_number',
                        'phone_number'
                    ]
                );

            });


        if ($mobileField) {

            $mobile =
                $request->input(
                    $mobileField->field_key
                );


            if ($mobile) {

                $duplicate =
                    LeadValue::where(
                        'campaign_field_id',
                        $mobileField->id
                    )
                    ->where(
                        'field_value',
                        $mobile
                    )
                    ->whereHas(
                        'lead',
                        function ($query) use ($campaign) {

                            $query->where(
                                'campaign_id',
                                $campaign->id
                            );

                        }
                    )
                    ->exists();


                if ($duplicate) {

                    return response()->json([

                        'success' => false,

                        'message' =>
                            'Duplicate lead already exists.'

                    ], 409);
                }
            }
        }


        DB::beginTransaction();


        try {

            $publisherId =
                $request->input('publisher_id');


            $advertiserId =
                $request->input('advertiser_id');


            /*
            |--------------------------------------------------------------------------
            | Create Lead
            |--------------------------------------------------------------------------
            */

            $lead = Lead::create([

                'campaign_id' =>
                    $campaign->id,

                'publisher_id' =>
                    $publisherId,

                'advertiser_id' =>
                    $advertiserId,

                'lead_reference' =>
                    'GL-' .
                    date('YmdHis') .
                    '-' .
                    strtoupper(
                        Str::random(6)
                    ),

                'status' =>
                    'new',

                'source' =>
                    $request->input(
                        'source',
                        'api'
                    ),

                'ip_address' =>
                    $request->ip(),

                'user_agent' =>
                    $request->userAgent(),

                'submitted_at' =>
                    now()
            ]);


            /*
            |--------------------------------------------------------------------------
            | Store Dynamic Fields
            |--------------------------------------------------------------------------
            */

            foreach (
                $campaign->fields
                as $field
            ) {

                $value =
                    $request->input(
                        $field->field_key
                    );


                LeadValue::create([

                    'lead_id' =>
                        $lead->id,

                    'campaign_field_id' =>
                        $field->id,

                    'field_value' =>
                        is_array($value)
                            ? json_encode($value)
                            : $value
                ]);
            }


            DB::commit();


            return response()->json([

                'success' =>
                    true,

                'message' =>
                    'Lead submitted successfully.',

                'data' => [

                    'lead_id' =>
                        $lead->id,

                    'lead_reference' =>
                        $lead->lead_reference,

                    'campaign_id' =>
                        $campaign->id,

                    'campaign' =>
                        $campaign->name,

                    'status' =>
                        $lead->status
                ]

            ], 201);


        } catch (\Exception $e) {

            DB::rollBack();


            return response()->json([

                'success' =>
                    false,

                'message' =>
                    'Unable to create lead.',

                'error' =>
                    $e->getMessage()

            ], 500);
        }
    }
}