<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPublisher()
    {
        return $this->role === 'publisher';
    }

    public function isAdvertiser()
    {
        return $this->role === 'advertiser';
    }

    public function isActive()
    {
        return $this->status == 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    */

    public function publisher()
    {
        return $this->hasOne(
            Publisher::class,
            'user_id'
        );
    }

    public function advertiser()
    {
        return $this->hasOne(
            Advertiser::class,
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
            'status'
        ])
        ->withTimestamps();
    }
    public function advertiserProfile()
    {
        return $this->hasOne(
            Advertiser::class,
            'user_id'
        );
    }


    public function publisherProfile()
    {
        return $this->hasOne(
            Publisher::class,
            'user_id'
        );
    }


    public function advertiserCampaigns()
    {
        return $this->hasMany(
            Campaign::class,
            'advertiser_id'
        );
    }


    public function publisherCampaigns()
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


    public function publisherLeads()
    {
        return $this->hasMany(
            Lead::class,
            'publisher_id'
        );
    }


    public function advertiserLeads()
    {
        return $this->hasMany(
            Lead::class,
            'advertiser_id'
        );
    }
}