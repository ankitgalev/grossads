<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    protected $table = 'advertisers';

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'phone',
        'website',
        'industry',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'notes',
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
        return $this->hasMany(
            Campaign::class,
            'advertiser_id'
        );
    }

    public function leads()
    {
        return $this->hasMany(
            Lead::class,
            'advertiser_id'
        );
    }
}