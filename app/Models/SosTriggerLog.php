<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosTriggerLog extends Model
{
    protected $table = 'sos_trigger_logs';

    protected $fillable = [
        'user_id',
        'ride_id',
        'user_role',
        'contact_name',
        'country_code',
        'contact_number',
        'latitude',
        'longitude',
        'product',
        'triggered_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
    ];
}
