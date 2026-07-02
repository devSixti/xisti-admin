<?php

namespace App\Helpers;

use App\Models\ServiceSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RideLifecycleHelper
{
    public static function rideExpiryMinutes(): int
    {
        $minutes = (int) (ServiceSettings::query()->value('ride_expiry') ?? 30);

        return max(5, min($minutes, 180));
    }

    public static function rideTimeoutFromNow(?int $minutes = null): string
    {
        $minutes ??= self::rideExpiryMinutes();
        $date = new \DateTime('now', new \DateTimeZone(config('app.timezone')));
        $date->modify('+'.$minutes.' minutes');

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Cancel pending rides that are no longer searchable (stale status=0 rows).
     */
    public static function expireStalePendingRides(): int
    {
        if (! Schema::hasTable('user_ride_booking')) {
            return 0;
        }

        $now = date('Y-m-d H:i:s');
        $fallbackCutoff = date('Y-m-d H:i:s', strtotime('-'.self::rideExpiryMinutes().' minutes'));

        $query = DB::table('user_ride_booking')->where('status', 0);

        $query->where(function ($q) use ($now, $fallbackCutoff) {
            $q->where(function ($expired) use ($now) {
                $expired->whereNotNull('ride_time_out')->where('ride_time_out', '<', $now);
            })->orWhere(function ($legacy) use ($fallbackCutoff) {
                $legacy->where(function ($nullTimeout) {
                    $nullTimeout->whereNull('ride_time_out')->orWhere('ride_time_out', '');
                })->where('created_at', '<', $fallbackCutoff);
            });
        });

        return $query->update([
            'status' => 4,
            'cancel_by' => 'system',
            'cancel_reason' => 'expired',
            'updated_at' => $now,
        ]);
    }
}
