<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarePricingRule extends Model
{
    protected $fillable = [
        'name',
        'rule_type',
        'multiplier',
        'conditions',
        'priority',
        'status',
    ];

    protected $casts = [
        'conditions' => 'array',
        'multiplier' => 'float',
        'status' => 'boolean',
    ];
}
