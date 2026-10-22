<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignField extends Model
{
    protected $table = 'campaign_fields';

    protected $fillable = [
        'campaign_id',
        'field_name',
        'field_key',
        'field_type',
        'options',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];


    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class,
            'campaign_id'
        );
    }


    public function values()
    {
        return $this->hasMany(
            LeadValue::class,
            'campaign_field_id'
        );
    }
}