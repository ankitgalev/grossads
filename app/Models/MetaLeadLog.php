<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaLeadLog extends Model
{
    protected $table = 'meta_lead_logs';

    protected $guarded = ['id'];

    public function integration()
    {
        return $this->belongsTo(
            MetaIntegration::class,
            'integration_id'
        );
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}