<?php

namespace App\Helpers;

use App\Classes\NotificationClass;
use App\Models\ProviderUserRunningService;
use App\Models\TransportRideBook;
use App\Models\UserRunningRide;
use Carbon\Carbon;

/**
 * Keeps user_running_ride / running_service pointers aligned with booking status.
 * Heals common stuck states (payment/rating after drop-off) that block home and account deletion.
 */
class RideSessionHelper
{
    /** @var list<int> */
    public const TERMINAL_STATUSES = [4, 9, 10];

    /** @var list<int> */
    public const ACTIVE_TRIP_STATUSES = [0, 1, 2, 3, 5, 6];

    /** @var list<int> */
    public const SETTLEMENT_STATUSES = [7, 8];

    public static function reconcileForUser(int $userId, ?NotificationClass $notificationClass = null): void
    {
        $notificationClass ??= app(NotificationClass::class);
        $generalSettings = request()->get('general_settings');

        foreach (UserRunningRide::query()->where('user_id', $userId)->get() as $pointer) {
            self::reconcilePointer((int) $pointer->booking_id, fn () => $pointer->delete(), $notificationClass, $generalSettings);
        }

        foreach (ProviderUserRunningService::query()->where('provider_id', $userId)->get() as $pointer) {
            self::reconcilePointer((int) $pointer->booking_id, fn () => $pointer->delete(), $notificationClass, $generalSettings);
        }

        TransportRideBook::query()
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('driver_id', $userId);
            })
            ->whereIn('status', self::SETTLEMENT_STATUSES)
            ->orderBy('id')
            ->each(function (TransportRideBook $ride) use ($notificationClass, $generalSettings) {
                self::healSettlementRide($ride, $notificationClass, $generalSettings);
            });
    }

    public static function hasBlockingRideActivity(int $userId): bool
    {
        self::reconcileForUser($userId);

        return TransportRideBook::query()
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('driver_id', $userId);
            })
            ->whereIn('status', array_merge(self::ACTIVE_TRIP_STATUSES, self::SETTLEMENT_STATUSES))
            ->exists();
    }

    public static function activePassengerRide(int $userId, ?NotificationClass $notificationClass = null): ?TransportRideBook
    {
        self::reconcileForUser($userId, $notificationClass);

        $pointer = UserRunningRide::query()->where('user_id', $userId)->first();
        if ($pointer === null) {
            return null;
        }

        $ride = TransportRideBook::query()->find($pointer->booking_id);
        if ($ride === null || in_array((int) $ride->status, self::TERMINAL_STATUSES, true)) {
            $pointer->delete();

            return null;
        }

        if (in_array((int) $ride->status, array_merge(self::ACTIVE_TRIP_STATUSES, self::SETTLEMENT_STATUSES), true)) {
            return $ride;
        }

        $pointer->delete();

        return null;
    }

    public static function healSettlementRide(
        TransportRideBook $ride,
        NotificationClass $notificationClass,
        ?object $generalSettings = null
    ): bool {
        if (! in_array((int) $ride->status, self::SETTLEMENT_STATUSES, true)) {
            return false;
        }

        if ((int) $ride->payment_status !== 1) {
            WalletSettlementHelper::markCashRidePaidIfNeeded($ride, $notificationClass, $generalSettings);
            $ride->refresh();
        }

        if ((int) $ride->status === 7 && (int) $ride->payment_status === 1) {
            $ride->status = 8;
            $ride->save();
        }

        if ((int) $ride->status === 8 && (int) $ride->payment_status === 1) {
            $rated = (int) ($ride->user_rating_status ?? 0) === 1;
            $stale = $ride->updated_at !== null
                && Carbon::parse($ride->updated_at)->lt(now()->subHours(12));

            if ($rated || $stale) {
                self::finalizeRide($ride);

                return true;
            }
        }

        return false;
    }

    private static function reconcilePointer(
        int $bookingId,
        callable $deletePointer,
        NotificationClass $notificationClass,
        ?object $generalSettings
    ): void {
        $ride = TransportRideBook::query()->find($bookingId);
        if ($ride === null) {
            $deletePointer();

            return;
        }

        if (in_array((int) $ride->status, self::TERMINAL_STATUSES, true)) {
            $deletePointer();

            return;
        }

        if (in_array((int) $ride->status, self::SETTLEMENT_STATUSES, true)) {
            self::healSettlementRide($ride, $notificationClass, $generalSettings);
            $ride->refresh();
            if (in_array((int) $ride->status, self::TERMINAL_STATUSES, true)) {
                $deletePointer();
            }
        }
    }

    private static function finalizeRide(TransportRideBook $ride): void
    {
        $ride->status = 9;
        $ride->save();

        UserRunningRide::query()->where('booking_id', $ride->id)->delete();
        ProviderUserRunningService::query()->where('booking_id', $ride->id)->delete();
    }
}
