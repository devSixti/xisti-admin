<?php

namespace App\Support;

/**
 * Normalizes implausible distance/time pairs from mobile routing APIs.
 */
final class TripMetricsSanitizer
{
    private const MAX_DISTANCE_KM = 200.0;

    private const URBAN_SPEED_KMH = 35.0;

    /**
     * @return array{0: float, 1: float} [distanceKm, minutes]
     */
    public static function sanitize(float $distanceKm, float $minutes): array
    {
        $distanceKm = max(0.0, $distanceKm);
        $minutes = max(0.0, $minutes);

        if ($distanceKm > 500.0) {
            $distanceKm = round($distanceKm / 1000.0, 2);
        }

        if ($minutes > 0.0 && $distanceKm > 0.0) {
            $speedKmh = ($distanceKm / $minutes) * 60.0;
            if ($speedKmh > 120.0) {
                $distanceKm = round(($minutes / 60.0) * self::URBAN_SPEED_KMH, 2);
            }
        }

        if ($distanceKm > self::MAX_DISTANCE_KM) {
            $distanceKm = self::MAX_DISTANCE_KM;
        }

        return [$distanceKm, $minutes];
    }
}
