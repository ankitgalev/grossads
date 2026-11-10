<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserLeadDelivery extends Model
{
    protected $table = 'advertiser_lead_deliveries';

    protected $fillable = [
        'lead_id',
        'campaign_id',
        'advertiser_id',
        'delivery_mode',
        'trigger_status',
        'status',
        'request_payload',
        'attempt_count',
        'max_attempts',
        'last_http_status',
        'last_response',
        'last_error',
        'next_attempt_at',
        'last_attempt_at',
        'locked_at',
        'delivered_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'attempt_count' => 'integer',
        'max_attempts' => 'integer',
        'last_http_status' => 'integer',
        'next_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'locked_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function logs()
    {
        return $this->hasMany(AdvertiserApiLog::class, 'delivery_id');
    }
}
