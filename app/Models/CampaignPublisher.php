<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignPublisher extends Model
{
    protected $table = 'campaign_publishers';

    protected $fillable = [
        'campaign_id',
        'publisher_id',
        'publisher_payout',
        'daily_limit',
        'total_limit',
        'api_key',
        'api_secret',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'publisher_payout' => 'decimal:2',
        'assigned_at' => 'datetime',
    ];


    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class,
            'campaign_id'
        );
    }


    public function publisher()
    {
        return $this->belongsTo(
            User::class,
            'publisher_id'
        );
    }
}