<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'campaign_id',
        'publisher_id',
        'advertiser_id',
        'lead_reference',
        'status',
        'source',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];
    

    protected $casts = [
        'submitted_at' => 'datetime',
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


    public function advertiser()
    {
        return $this->belongsTo(
            Advertiser::class,
            'advertiser_id'
        );
    }


    public function values()
    {
        return $this->hasMany(
            LeadValue::class,
            'lead_id'
        );
    }
}