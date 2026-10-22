<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $table = 'publishers';

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'phone',
        'website',
        'callback_url',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    public function campaigns()
    {
        return $this->belongsToMany(
            Campaign::class,
            'campaign_publishers',
            'publisher_id',
            'campaign_id'
        )
        ->withPivot([
            'publisher_payout',
            'daily_limit',
            'total_limit',
            'api_key',
            'api_secret',
            'assigned_at',
            'status',
        ])
        ->withTimestamps();
    }


    public function leads()
    {
        return $this->hasMany(
            Lead::class,
            'publisher_id'
        );
    }
}