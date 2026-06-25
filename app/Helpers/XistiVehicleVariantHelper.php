<?php

namespace App\Helpers;

/**
 * Passenger vehicle matrix slugs (mobile catalog + driver registration).
 */
class XistiVehicleVariantHelper
{
    public const CARRO_ECO = 'carro_eco';

    public const CARRO_COMODO = 'carro_comodo';

    public const CARRO_ECONOMICO = 'carro_economico';

    public const MOTO_ALTO = 'moto_alto_cilindraje';

    public const MOTO_BAJO = 'moto_bajo_cilindraje';

    public const MOTO_MEDIO = 'moto_medio_cilindraje';

    public const BICICLETA = 'bicicleta';

    public static function normalize(?string $variant): string
    {
        return trim((string) $variant);
    }

    public static function isTaxiEligibleVariant(?string $variant): bool
    {
        $v = self::normalize($variant);

        return $v === self::CARRO_ECO || $v === self::CARRO_ECONOMICO;
    }

    public static function labelFor(?string $variant, ?string $fallback = null): string
    {
        $v = self::normalize($variant);
        $labels = [
            self::CARRO_ECO => 'Carro eco',
            self::CARRO_COMODO => 'Carro cómodo',
            self::CARRO_ECONOMICO => 'Carro económico',
            self::MOTO_ALTO => 'Moto alto cilindraje',
            self::MOTO_BAJO => 'Moto bajo cilindraje',
            self::MOTO_MEDIO => 'Moto',
            self::BICICLETA => 'Bicicleta',
        ];
        if ($v !== '' && isset($labels[$v])) {
            return $labels[$v];
        }

        return trim((string) $fallback) !== '' ? trim((string) $fallback) : 'Viaje';
    }

    /** API flag: ride was booked as taxi-class car (eco / económico). */
    public static function rideTaxiFlag(?string $variant): int
    {
        return self::isTaxiEligibleVariant($variant) ? 1 : 0;
    }

    /**
     * Match drivers to a transport ride's vehicle matrix slug (eco, económico, moto alto, etc.).
     */
    public static function applyTransportVariantDriverFilter($query, ?string $rideVariant, string $driverTable = 'transport_driver_details'): void
    {
        $variant = self::normalize($rideVariant);
        if ($variant === '' || ! \Illuminate\Support\Facades\Schema::hasColumn($driverTable, 'delivery_variant')) {
            return;
        }
        $query->where(function ($q) use ($variant, $driverTable) {
            $q->where("{$driverTable}.delivery_variant", $variant)
                ->orWhereNull("{$driverTable}.delivery_variant")
                ->orWhere("{$driverTable}.delivery_variant", '');
        });
    }

    /**
     * Match available rides to a driver's registered matrix slug.
     */
    public static function applyTransportVariantRideFilter($query, ?string $driverVariant, string $rideTable = 'user_ride_booking'): void
    {
        $variant = self::normalize($driverVariant);
        if ($variant === '' || ! \Illuminate\Support\Facades\Schema::hasColumn($rideTable, 'delivery_variant')) {
            return;
        }
        $query->where(function ($q) use ($variant, $rideTable) {
            $q->where("{$rideTable}.delivery_variant", $variant)
                ->orWhereNull("{$rideTable}.delivery_variant")
                ->orWhere("{$rideTable}.delivery_variant", '');
        });
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function enrichRideRow(array $row, ?string $fallbackServiceName = null): array
    {
        $variant = self::normalize($row['delivery_variant'] ?? '');
        if ($variant !== '') {
            $row['delivery_variant'] = $variant;
        }
        $row['vehicle_variant'] = $variant;
        $row['service_name'] = self::labelFor(
            $variant,
            $fallbackServiceName ?? ($row['service_name'] ?? null)
        );
        $row['is_taxi'] = self::rideTaxiFlag($variant);

        return $row;
    }

    public static function deliveryOptionsForApi(string $langPrefix = ''): array
    {
        $iconBase = url('/assets/images/vehicle-service/');

        return [
            [
                'vehicle_service_id' => 3,
                'label' => 'Moto',
                'service_icon' => $iconBase . '/bike.png?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION,
                'delivery_variant' => self::MOTO_MEDIO,
            ],
            [
                'vehicle_service_id' => 1,
                'label' => 'Carro',
                'service_icon' => $iconBase . '/taxi.png?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION,
                'delivery_variant' => self::CARRO_ECONOMICO,
            ],
            [
                'vehicle_service_id' => 4,
                'label' => 'Bicicleta',
                'service_icon' => $iconBase . '/courier.png?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION,
                'delivery_variant' => self::BICICLETA,
            ],
        ];
    }
}
