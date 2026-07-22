<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorldCurrency extends Model
{
    protected $table = 'world_currency';

    public static function forUser($user): ?self
    {
        return \App\Support\UserCurrencyResolver::forUser($user);
    }
}
