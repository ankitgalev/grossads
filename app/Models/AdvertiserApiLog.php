<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserApiLog extends Model
{
    protected $table = 'advertiser_api_logs';

    protected $fillable = [
        'delivery_id',
        'attempt_number',
        'request_url',
        'http_method',
        'request_payload',
        'http_status',
        'response_body',
        'is_success',
        'error_message',
        'attempted_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'attempt_number' => 'integer',
        'http_status' => 'integer',
        'is_success' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(
            AdvertiserLeadDelivery::class,
            'delivery_id'
        );
    }
}
