<?php

namespace App\Helpers;

use App\Models\TransportRideBook;
use Illuminate\Http\JsonResponse;

class RideAccessHelper
{
    public static function findRideOrNull(int $rideId): ?TransportRideBook
    {
        return TransportRideBook::query()->where('id', $rideId)->first();
    }

    public static function denyForbidden(): JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => __('user_messages.9'),
            'message_code' => 403,
        ], 403);
    }

    public static function assertPassengerOwnsRide(int $userId, int $rideId): ?JsonResponse
    {
        $ride = self::findRideOrNull($rideId);
        if ($ride === null) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.26'),
                'message_code' => 26,
            ]);
        }
        if ((int) $ride->user_id !== $userId) {
            return self::denyForbidden();
        }

        return null;
    }

    public static function assertDriverAssignedToRide(int $driverUserId, int $rideId): ?JsonResponse
    {
        $ride = self::findRideOrNull($rideId);
        if ($ride === null) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.26'),
                'message_code' => 26,
            ]);
        }
        if ((int) $ride->driver_id !== $driverUserId) {
            return self::denyForbidden();
        }

        return null;
    }

    public static function assertRideParticipant(int $userId, int $rideId): ?JsonResponse
    {
        $ride = self::findRideOrNull($rideId);
        if ($ride === null) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.26'),
                'message_code' => 26,
            ]);
        }
        if ((int) $ride->user_id === $userId || (int) $ride->driver_id === $userId) {
            return null;
        }

        return self::denyForbidden();
    }
}
