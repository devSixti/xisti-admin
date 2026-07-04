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

        $expiredIds = DB::table('user_ride_booking')
            ->where('status', 0)
            ->where(function ($q) use ($now, $fallbackCutoff) {
                $q->where(function ($expired) use ($now) {
                    $expired->whereNotNull('ride_time_out')->where('ride_time_out', '<', $now);
                })->orWhere(function ($legacy) use ($fallbackCutoff) {
                    $legacy->where(function ($nullTimeout) {
                        $nullTimeout->whereNull('ride_time_out')->orWhere('ride_time_out', '');
                    })->where('created_at', '<', $fallbackCutoff);
                });
            })
            ->pluck('id')
            ->all();

        if (empty($expiredIds)) {
            return 0;
        }

        DB::table('user_ride_booking')->whereIn('id', $expiredIds)->update([
            'status' => 4,
            'cancel_by' => 'system',
            'cancel_reason' => 'expired',
            'updated_at' => $now,
        ]);

        if (Schema::hasTable('user_running_ride')) {
            DB::table('user_running_ride')->whereIn('booking_id', $expiredIds)->delete();
        }
        if (Schema::hasTable('running_service')) {
            DB::table('running_service')->whereIn('booking_id', $expiredIds)->delete();
        }

        return count($expiredIds);
    }

    /**
     * Remove orphan user_running_ride rows where the booking is already cancelled.
     */
    public static function purgeOrphanRunningRides(): int
    {
        if (! Schema::hasTable('user_running_ride') || ! Schema::hasTable('user_ride_booking')) {
            return 0;
        }

        $orphanIds = DB::table('user_running_ride')
            ->join('user_ride_booking', 'user_running_ride.booking_id', '=', 'user_ride_booking.id')
            ->whereIn('user_ride_booking.status', [4, 6])
            ->pluck('user_running_ride.id')
            ->all();

        if (empty($orphanIds)) {
            return 0;
        }

        return DB::table('user_running_ride')->whereIn('id', $orphanIds)->delete();
    }
}
