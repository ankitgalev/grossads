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
        'is_duplicate',
        'duplicate_of_lead_id',
        'duplicate_remark',
        'source',
        'ip_address',
        'user_agent',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_duplicate' => 'boolean',
    ];

    public function advertiserDelivery()
    {
        return $this->hasOne(AdvertiserLeadDelivery::class, 'lead_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class, 'advertiser_id');
    }

    public function values()
    {
        return $this->hasMany(LeadValue::class, 'lead_id');
    }

    public function duplicateOf()
    {
        return $this->belongsTo(self::class, 'duplicate_of_lead_id');
    }

    public function duplicates()
    {
        return $this->hasMany(self::class, 'duplicate_of_lead_id');
    }
}
