<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportDriverDetails extends Model
{
    protected $table = 'transport_driver_details';

    public function getUserDetails() :BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
