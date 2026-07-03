<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRole extends Model
{
    protected $fillable = ['slug', 'name', 'legacy_role', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function modulePermissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class, 'role_id');
    }
}
