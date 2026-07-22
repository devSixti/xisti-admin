<?php

namespace App\Support;

use App\Models\TransportDriverDetails;
use Illuminate\Http\Request;

/**
 * Keeps driver GPS in sync for available-ride matching.
 */
final class DriverLocationHelper
{
    public static function parseCoord(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $parsed = is_numeric($value) ? (float) $value : null;

        return $parsed;
    }

    public static function isValid(?float $lat, ?float $lng): bool
    {
        return $lat !== null
            && $lng !== null
            && abs($lat) >= 0.01
            && abs($lng) >= 0.01
            && abs($lat) <= 90.0
            && abs($lng) <= 180.0;
    }

    /**
     * Prefer request GPS, persist to driver row, fall back to stored coords.
     *
     * @return array{0: float, 1: float}
     */
    public static function syncFromRequest(Request $request, ?object $driverDetails): array
    {
        $reqLat = self::parseCoord($request->get('current_lat') ?? $request->get('latitude'));
        $reqLng = self::parseCoord(
            $request->get('current_long')
            ?? $request->get('current_lng')
            ?? $request->get('longitude')
        );

        if (self::isValid($reqLat, $reqLng)) {
            self::persistDriverCoords((int) $request->get('user_id'), $reqLat, $reqLng, $driverDetails);

            return [$reqLat, $reqLng];
        }

        $storedLat = self::parseCoord($driverDetails->current_lat ?? null);
        $storedLng = self::parseCoord($driverDetails->current_long ?? null);
        if (self::isValid($storedLat, $storedLng)) {
            return [$storedLat, $storedLng];
        }

        return [0.0, 0.0];
    }

    private static function persistDriverCoords(int $userId, float $lat, float $lng, ?object $driverDetails): void
    {
        if ($userId <= 0) {
            return;
        }
        $prevLat = self::parseCoord($driverDetails->current_lat ?? null) ?? 0.0;
        $prevLng = self::parseCoord($driverDetails->current_long ?? null) ?? 0.0;
        if (abs($prevLat - $lat) < 0.00005 && abs($prevLng - $lng) < 0.00005) {
            return;
        }
        TransportDriverDetails::query()
            ->where('user_id', $userId)
            ->update([
                'current_lat' => $lat,
                'current_long' => $lng,
                'updated_at' => now(),
            ]);
        if ($driverDetails !== null) {
            $driverDetails->current_lat = $lat;
            $driverDetails->current_long = $lng;
        }
    }
}
