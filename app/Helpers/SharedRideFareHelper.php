<?php

namespace App\Helpers;

/**
 * Recommends per-person fare for shared intercity rides (XISTI Compartir).
 */
class SharedRideFareHelper
{
    /** @var array<string, int> variant => COP per km (fuel/energy estimate) */
    private const COST_PER_KM = [
        XistiVehicleVariantHelper::CARRO_ECO => 180,
        XistiVehicleVariantHelper::CARRO_ECONOMICO => 220,
        XistiVehicleVariantHelper::CARRO_COMODO => 280,
    ];

    /**
     * Known Antioquia / regional routes (km) — extend as needed.
     *
     * @var array<string, int>
     */
    private const ROUTE_KM = [
        'medellin|san jeronimo' => 82,
        'medellin|san jerónimo' => 82,
        'medellin|santa fe de antioquia' => 79,
        'medellin|rionegro' => 38,
        'medellin|marinilla' => 48,
        'medellin|guatape' => 79,
        'medellin|envigado' => 12,
        'medellin|itagui' => 14,
        'medellin|bello' => 14,
    ];

    /** @var array<string, int> route key => toll COP (one way) */
    private const ROUTE_TOLLS = [
        'medellin|san jeronimo' => 28000,
        'medellin|san jerónimo' => 28000,
        'medellin|santa fe de antioquia' => 32000,
        'medellin|guatape' => 18000,
    ];

    /**
     * @return array{
     *   distance_km: float,
     *   tolls_estimate: float,
     *   fuel_estimate: float,
     *   total_trip_cost: float,
     *   recommended_fare_per_person: float,
     *   min_fare_per_person: float,
     *   max_fare_per_person: float
     * }
     */
    public static function recommendPerPerson(
        string $originTown,
        string $destinationTown,
        string $vehicleVariant,
        int $seatsTotal,
        bool $isWeekend = false
    ): array {
        $variant = XistiVehicleVariantHelper::normalize($vehicleVariant);
        if ($variant === '' || ! isset(self::COST_PER_KM[$variant])) {
            $variant = XistiVehicleVariantHelper::CARRO_ECONOMICO;
        }

        $seats = max(1, min(8, $seatsTotal));
        $distanceKm = self::estimateDistanceKm($originTown, $destinationTown);
        $tolls = self::estimateTolls($originTown, $destinationTown);
        $fuel = round($distanceKm * self::COST_PER_KM[$variant], 0);

        if ($isWeekend) {
            $fuel = round($fuel * 1.05, 0);
        }

        $total = $fuel + $tolls;
        $basePerPerson = $total / $seats;

        return [
            'distance_km' => $distanceKm,
            'tolls_estimate' => $tolls,
            'fuel_estimate' => $fuel,
            'total_trip_cost' => $total,
            'recommended_fare_per_person' => round($basePerPerson, -2),
            'min_fare_per_person' => round($basePerPerson * 0.85, -2),
            'max_fare_per_person' => round($basePerPerson * 1.15, -2),
        ];
    }

    public static function estimateDistanceKm(string $origin, string $destination): float
    {
        $key = self::routeKey($origin, $destination);
        if (isset(self::ROUTE_KM[$key])) {
            return (float) self::ROUTE_KM[$key];
        }

        $originNorm = self::normalizePlace($origin);
        $destNorm = self::normalizePlace($destination);

        if (str_contains($originNorm, 'medellin') || str_contains($destNorm, 'medellin')) {
            return 55.0;
        }

        return 40.0;
    }

    public static function estimateTolls(string $origin, string $destination): float
    {
        $key = self::routeKey($origin, $destination);

        return (float) (self::ROUTE_TOLLS[$key] ?? 0);
    }

    private static function routeKey(string $origin, string $destination): string
    {
        $a = self::normalizePlace($origin);
        $b = self::normalizePlace($destination);

        return $a < $b ? "$a|$b" : "$b|$a";
    }

    private static function normalizePlace(string $value): string
    {
        $v = mb_strtolower(trim($value));
        $v = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $v);
        $v = preg_replace('/\s+/', ' ', $v) ?? $v;

        return $v;
    }
}
