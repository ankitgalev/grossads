<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignField;
use App\Models\Advertiser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Campaign List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $campaigns = Campaign::with([
            'advertiser'
        ])
        ->withCount([
            'fields',
            'publishers',
            'leads'
        ])
        ->latest()
        ->paginate(20);

        return view(
            'admin.campaigns.index',
            compact('campaigns')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Campaign
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $advertisers = Advertiser::where(
            'status',
            1
        )
        ->orderBy('company_name')
        ->get();

        return view(
            'admin.campaigns.create',
            compact('advertisers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Campaign
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'advertiser_id' => [
                'required',
                'exists:advertisers,id'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'campaign_type' => [
                'required',
                'in:lead,call,survey,other'
            ],

            'status' => [
                'required',
                'in:active,inactive,paused'
            ],

            'publisher_payout' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'advertiser_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'postback_url' => [
                'nullable',
                'url',
                'max:2000'
            ],

            'fields' => [
                'nullable',
                'array'
            ],

            'fields.*.field_name' => [
                'required',
                'string',
                'max:255'
            ],

            'fields.*.field_key' => [
                'required',
                'string',
                'max:100'
            ],

            'fields.*.field_type' => [
                'required',
                'in:text,email,phone,number,date,select,textarea'
            ],

            'fields.*.options' => [
                'nullable',
                'string'
            ],

            'fields.*.is_required' => [
                'nullable'
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $request
        ) {

            /*
            |--------------------------------------------------------------------------
            | Generate unique slug
            |--------------------------------------------------------------------------
            */

            $slug = Str::slug(
                $validated['name']
            );

            $originalSlug = $slug;

            $counter = 1;

            while (
                Campaign::where(
                    'slug',
                    $slug
                )->exists()
            ) {
                $slug = $originalSlug
                    . '-'
                    . $counter++;

            }


            /*
            |--------------------------------------------------------------------------
            | Create Campaign
            |--------------------------------------------------------------------------
            */

            $campaign = Campaign::create([

                'advertiser_id' => $validated[
                    'advertiser_id'
                ],

                'name' => $validated[
                    'name'
                ],

                'slug' => $slug,

                'description' => $validated[
                    'description'
                ] ?? null,

                'campaign_type' => $validated[
                    'campaign_type'
                ],

                'status' => $validated[
                    'status'
                ],

                'publisher_payout' => $validated[
                    'publisher_payout'
                ] ?? 0,

                'advertiser_price' => $validated[
                    'advertiser_price'
                ] ?? 0,

                'postback_url' => $validated[
                    'postback_url'
                ] ?? null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Dynamic Fields
            |--------------------------------------------------------------------------
            */

            if (
                !empty(
                    $validated['fields']
                )
            ) {

                foreach (
                    $validated['fields']
                    as $index => $field
                ) {

                    $options = null;

                    if (
                        !empty(
                            $field['options']
                        )
                    ) {

                        $options = json_encode(
                            array_values(
                                array_filter(
                                    array_map(
                                        'trim',
                                        preg_split(
                                            '/[\r\n,]+/',
                                            $field['options']
                                        )
                                    )
                                )
                            )
                        );
                    }


                    CampaignField::create([

                        'campaign_id' => $campaign->id,

                        'field_name' => $field[
                            'field_name'
                        ],

                        'field_key' => Str::snake(
                            $field['field_key']
                        ),

                        'field_type' => $field[
                            'field_type'
                        ],

                        'options' => $options,

                        'is_required' =>
                            !empty(
                                $field['is_required']
                            ),

                        'sort_order' => $index,
                    ]);
                }
            }
        });


        return redirect()
            ->route('admin.campaigns.index')
            ->with(
                'success',
                'Campaign created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $campaign = Campaign::with([
            'advertiser',
            'fields',
            'publishers',
            'leads'
        ])
        ->findOrFail($id);

        return view(
            'admin.campaigns.show',
            compact('campaign')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $campaign = Campaign::with(
            'fields'
        )->findOrFail($id);

        $advertisers = Advertiser::where(
            'status',
            1
        )
        ->orderBy('company_name')
        ->get();

        return view(
            'admin.campaigns.edit',
            compact(
                'campaign',
                'advertisers'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $campaign = Campaign::findOrFail(
            $id
        );


        $validated = $request->validate([

            'advertiser_id' => [
                'required',
                'exists:advertisers,id'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'campaign_type' => [
                'required',
                'in:lead,call,survey,other'
            ],

            'status' => [
                'required',
                'in:active,inactive,paused'
            ],

            'publisher_payout' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'advertiser_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'postback_url' => [
                'nullable',
                'url',
                'max:2000'
            ],

            'fields' => [
                'nullable',
                'array'
            ],

            'fields.*.field_name' => [
                'required',
                'string',
                'max:255'
            ],

            'fields.*.field_key' => [
                'required',
                'string',
                'max:100'
            ],

            'fields.*.field_type' => [
                'required',
                'in:text,email,phone,number,date,select,textarea'
            ],

            'fields.*.options' => [
                'nullable',
                'string'
            ],

            'fields.*.is_required' => [
                'nullable'
            ],

        ]);


        DB::transaction(function () use (
            $campaign,
            $validated
        ) {

            $slug = Str::slug(
                $validated['name']
            );

            $originalSlug = $slug;

            $counter = 1;

            while (
                Campaign::where(
                    'slug',
                    $slug
                )
                ->where(
                    'id',
                    '!=',
                    $campaign->id
                )
                ->exists()
            ) {

                $slug = $originalSlug
                    . '-'
                    . $counter++;
            }


            $campaign->update([

                'advertiser_id' => $validated[
                    'advertiser_id'
                ],

                'name' => $validated[
                    'name'
                ],

                'slug' => $slug,

                'description' => $validated[
                    'description'
                ] ?? null,

                'campaign_type' => $validated[
                    'campaign_type'
                ],

                'status' => $validated[
                    'status'
                ],

                'publisher_payout' => $validated[
                    'publisher_payout'
                ] ?? 0,

                'advertiser_price' => $validated[
                    'advertiser_price'
                ] ?? 0,

                'postback_url' => $validated[
                    'postback_url'
                ] ?? null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Replace Dynamic Fields
            |--------------------------------------------------------------------------
            */

            $campaign->fields()->delete();


            if (
                !empty(
                    $validated['fields']
                )
            ) {

                foreach (
                    $validated['fields']
                    as $index => $field
                ) {

                    $options = null;

                    if (
                        !empty(
                            $field['options']
                        )
                    ) {

                        $options = json_encode(
                            array_values(
                                array_filter(
                                    array_map(
                                        'trim',
                                        preg_split(
                                            '/[\r\n,]+/',
                                            $field['options']
                                        )
                                    )
                                )
                            )
                        );
                    }


                    CampaignField::create([

                        'campaign_id' =>
                            $campaign->id,

                        'field_name' =>
                            $field['field_name'],

                        'field_key' =>
                            Str::snake(
                                $field['field_key']
                            ),

                        'field_type' =>
                            $field['field_type'],

                        'options' =>
                            $options,

                        'is_required' =>
                            !empty(
                                $field['is_required']
                            ),

                        'sort_order' =>
                            $index,
                    ]);
                }
            }
        });


        return redirect()
            ->route(
                'admin.campaigns.index'
            )
            ->with(
                'success',
                'Campaign updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail(
            $id
        );

        $campaign->delete();

        return redirect()
            ->route(
                'admin.campaigns.index'
            )
            ->with(
                'success',
                'Campaign deleted successfully.'
            );
    }
}