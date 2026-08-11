<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMfaChallenge extends Model
{
    protected $table = 'admin_mfa_challenges';

    protected $fillable = [
        'admin_id',
        'token_hash',
        'ip',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
