<?php

namespace App\Helpers;

class AcarreoHelper
{
    public const MODE = 'acarreos';

    public const ERRAND_ACARREO = 'acarreo';

    public const VARIANT_MOTOCARGUERO = 'motocarguero';

    public const VARIANT_CAMION = 'camion';

    public const VARIANT_JAULA = 'jaula';

    public static function allowedVariants(): array
    {
        return [
            self::VARIANT_MOTOCARGUERO,
            self::VARIANT_CAMION,
            self::VARIANT_JAULA,
        ];
    }

    public static function normalizeVariant(?string $variant): ?string
    {
        $key = strtolower(trim((string) $variant));
        if ($key === 'motocarro') {
            $key = self::VARIANT_MOTOCARGUERO;
        }

        return in_array($key, self::allowedVariants(), true) ? $key : null;
    }

    public static function shouldPersistCourierRow(?string $errandType): bool
    {
        return strtolower((string) $errandType) === self::ERRAND_ACARREO;
    }

    public static function driverCanReceiveAcarreoRequests(object $driverDetails): bool
    {
        $serviceId = (int) ($driverDetails->service_id ?? 0);
        if ($serviceId <= 0 && isset($driverDetails->vehicle_type_id)) {
            $serviceId = (int) \Illuminate\Support\Facades\DB::table('transport_vehicle_type')
                ->where('id', (int) $driverDetails->vehicle_type_id)
                ->value('service_id');
        }

        if ($serviceId <= 0) {
            return false;
        }

        $mode = (string) \Illuminate\Support\Facades\DB::table('vehicle_services')
            ->where('id', $serviceId)
            ->value('service_mode');

        return $mode === self::MODE;
    }

    public static function passengerDisclaimer(string $language = 'es'): string
    {
        if (str_starts_with($language, 'es')) {
            return 'Consiste en traslado de carga pesada.';
        }

        return 'This is heavy-load hauling. Set origin, destination, description and your offered fare.';
    }
}
