<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdvertiserLeadDelivery;
class Campaign extends Model
{
    protected $table = 'campaigns';

    protected $fillable = [
        'advertiser_id',
        'name',
        'slug',
        'description',
        'campaign_type',
        'status',
        'publisher_payout',
        'advertiser_price',
        'postback_url',
        'advertiser_delivery_enabled',
        'advertiser_delivery_mode',
        'advertiser_delivery_status',
        'advertiser_api_url',
        'advertiser_http_method',
        'advertiser_payload_format',
        'advertiser_auth_type',
        'advertiser_auth_config',
        'advertiser_field_mapping',
        'advertiser_timeout',
        'advertiser_retry_attempts',
        'advertiser_cron_batch_size',
    ];

    protected $casts = [
        'publisher_payout' => 'decimal:2',
        'advertiser_price' => 'decimal:2',
        'advertiser_delivery_enabled' => 'boolean',
        'advertiser_auth_config' => 'array',
        'advertiser_field_mapping' => 'array',
        'advertiser_timeout' => 'integer',
        'advertiser_retry_attempts' => 'integer',
        'advertiser_cron_batch_size' => 'integer',
    ];


    /*
    |--------------------------------------------------------------------------
    | Advertiser
    |--------------------------------------------------------------------------
    */
    public function advertiserDeliveries()
    {
        return $this->hasMany(
            \App\Models\AdvertiserLeadDelivery::class,
            'campaign_id'
        );
    }
    public function advertiser()
    {
        return $this->belongsTo(
            Advertiser::class,
            'advertiser_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Campaign Fields
    |--------------------------------------------------------------------------
    */

    public function fields()
    {
        return $this->hasMany(
            CampaignField::class,
            'campaign_id'
        )->orderBy('sort_order');
    }


    /*
    |--------------------------------------------------------------------------
    | Publishers
    |--------------------------------------------------------------------------
    */

    // public function publishers()
    // {
    //     return $this->belongsToMany(
    //         User::class,
    //         'campaign_publishers',
    //         'campaign_id',
    //         'publisher_id'
    //     )
    //     ->withPivot([
    //         'publisher_payout',
    //         'daily_limit',
    //         'total_limit',
    //         'api_key',
    //         'api_secret',
    //         'assigned_at',
    //         'status',
    //     ])
    //     ->withTimestamps();
    // }
    public function publishers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'campaign_publishers',
            'campaign_id',
            'publisher_id'
        )->withPivot(
            'id',
            'publisher_payout',
            'daily_limit',
            'total_limit',
            'api_key',
            'api_secret',
            'assigned_at',
            'status'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Leads
    |--------------------------------------------------------------------------
    */

    public function leads()
    {
        return $this->hasMany(
            Lead::class,
            'campaign_id'
        );
    }
}