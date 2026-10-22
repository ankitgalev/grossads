<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'publisher_payout' => 'decimal:2',
        'advertiser_price' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Advertiser
    |--------------------------------------------------------------------------
    */

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