<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadValue extends Model
{
    protected $table = 'lead_values';

    protected $fillable = [
        'lead_id',
        'campaign_field_id',
        'field_value',
    ];


    public function lead()
    {
        return $this->belongsTo(
            Lead::class,
            'lead_id'
        );
    }


    public function field()
    {
        return $this->belongsTo(
            CampaignField::class,
            'campaign_field_id'
        );
    }
}