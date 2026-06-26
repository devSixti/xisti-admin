<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCommissionRate extends Model
{
    protected $table = 'vehicle_commission_rates';

    protected $fillable = [
        'variant_key',
        'label',
        'vehicle_service_id',
        'commission_percent',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'vehicle_service_id' => 'integer',
        'commission_percent' => 'float',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];
}
