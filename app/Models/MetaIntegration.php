<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MetaIntegration extends Model
{
    protected $table = 'meta_integrations';

    protected $guarded = ['id'];

    protected $hidden = [
        'app_secret',
        'page_access_token',
        'verify_token'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function setAppSecretAttribute($value)
    {
        $this->attributes['app_secret'] =
            $value ? Crypt::encryptString($value) : '';
    }

    public function getAppSecretAttribute($value)
    {
        return $this->decryptSecret($value);
    }

    public function setPageAccessTokenAttribute($value)
    {
        $this->attributes['page_access_token'] =
            $value ? Crypt::encryptString($value) : '';
    }

    public function getPageAccessTokenAttribute($value)
    {
        return $this->decryptSecret($value);
    }

    public function setVerifyTokenAttribute($value)
    {
        $this->attributes['verify_token'] =
            $value ? Crypt::encryptString($value) : '';
    }

    public function getVerifyTokenAttribute($value)
    {
        return $this->decryptSecret($value);
    }

    private function decryptSecret($value)
    {
        if (!$value) {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Supports detecting old/plain-text values during setup.
            return '';
        }
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function campaignPublisher()
    {
        return $this->belongsTo(
            CampaignPublisher::class,
            'campaign_publisher_id'
        );
    }

    public function logs()
    {
        return $this->hasMany(MetaLeadLog::class, 'integration_id');
    }
}