<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table has no auto-increment id (composite provider_id + user_id + booking_id).
 * Override delete() so Eloquent does not emit WHERE id = ?.
 */
class ProviderUserRunningService extends Model
{
    protected $table = 'running_service';

    public $incrementing = false;

    protected $fillable = [
        'provider_id',
        'user_id',
        'booking_id',
    ];

    public function getKeyName()
    {
        return 'booking_id';
    }

    public function delete()
    {
        if ($this->provider_id === null || $this->booking_id === null) {
            return false;
        }

        $query = static::query()
            ->where('provider_id', $this->provider_id)
            ->where('booking_id', $this->booking_id);

        if ($this->user_id !== null) {
            $query->where('user_id', $this->user_id);
        }

        return $query->delete() > 0;
    }
}
