<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcarreoVehicleHelper
{
    public static function acarreosServiceId(): int
    {
        if (! Schema::hasTable('vehicle_services')) {
            return 0;
        }

        return (int) DB::table('vehicle_services')
            ->where('service_mode', AcarreoHelper::MODE)
            ->where('status', 1)
            ->value('id');
    }

    public static function optionsForApi(string $langPrefix = ''): array
    {
        $iconBase = url('/assets/images/vehicle-service/');
        $v = DeliveryVehicleHelper::ICON_CACHE_VERSION;
        $isEs = $langPrefix === '' || str_starts_with($langPrefix, 'es');
        $serviceId = self::acarreosServiceId();

        return [
            [
                'vehicle_service_id' => $serviceId > 0 ? $serviceId : 5,
                'acarreo_variant' => AcarreoHelper::VARIANT_MOTOCARGUERO,
                'label' => $isEs ? 'Motocarguero' : 'Motocarguero',
                'service_icon' => $iconBase . '/motocarguero.png?v=' . $v,
            ],
            [
                'vehicle_service_id' => $serviceId,
                'acarreo_variant' => AcarreoHelper::VARIANT_CAMION,
                'label' => $isEs ? 'Camión' : 'Truck',
                'service_icon' => $iconBase . '/camion_acarreo.png?v=' . $v,
            ],
            [
                'vehicle_service_id' => $serviceId,
                'acarreo_variant' => AcarreoHelper::VARIANT_JAULA,
                'label' => $isEs ? 'Jaula' : 'Cage truck',
                'service_icon' => $iconBase . '/jaula_acarreo.png?v=' . $v,
            ],
        ];
    }

    public static function driverVariantForVehicleType(int $vehicleTypeId): ?string
    {
        return self::normalizeVariantFromVehicleTypeName(
            (string) DB::table('transport_vehicle_type')->where('id', $vehicleTypeId)->value('name')
        );
    }

    public static function normalizeVariantFromVehicleTypeName(string $name): ?string
    {
        $lower = strtolower(trim($name));
        if (str_contains($lower, 'motocarg') || str_contains($lower, 'motocarr')) {
            return AcarreoHelper::VARIANT_MOTOCARGUERO;
        }
        if (str_contains($lower, 'camion') || str_contains($lower, 'camión')) {
            return AcarreoHelper::VARIANT_CAMION;
        }
        if (str_contains($lower, 'jaula')) {
            return AcarreoHelper::VARIANT_JAULA;
        }

        return null;
    }

    public static function driverMatchesAcarreoRequest(int $driverVehicleTypeId, ?string $requestedVariant): bool
    {
        $variant = AcarreoHelper::normalizeVariant($requestedVariant);
        if ($variant === null) {
            return false;
        }

        if (! Schema::hasTable('transport_vehicle_type')) {
            return true;
        }

        $row = DB::table('transport_vehicle_type')->where('id', $driverVehicleTypeId)->first();
        if ($row === null) {
            return false;
        }

        $name = strtolower((string) ($row->name ?? ''));

        return match ($variant) {
            AcarreoHelper::VARIANT_MOTOCARGUERO => str_contains($name, 'motocarg') || str_contains($name, 'motocarr') || (int) $row->service_id === 5,
            AcarreoHelper::VARIANT_CAMION => str_contains($name, 'camion') || str_contains($name, 'camión'),
            AcarreoHelper::VARIANT_JAULA => str_contains($name, 'jaula'),
            default => false,
        };
    }

    public static function applyVariantDriverFilter($query, ?string $requestedVariant, string $vehicleTypeTable = 'transport_vehicle_type'): void
    {
        $variant = AcarreoHelper::normalizeVariant($requestedVariant);
        if ($variant === null) {
            return;
        }

        $query->where(function ($variantScope) use ($variant, $vehicleTypeTable) {
            match ($variant) {
                AcarreoHelper::VARIANT_MOTOCARGUERO => $variantScope
                    ->whereRaw("LOWER({$vehicleTypeTable}.name) LIKE ?", ['%motocarg%'])
                    ->orWhereRaw("LOWER({$vehicleTypeTable}.name) LIKE ?", ['%motocarr%'])
                    ->orWhere("{$vehicleTypeTable}.service_id", 5),
                AcarreoHelper::VARIANT_CAMION => $variantScope
                    ->whereRaw("LOWER({$vehicleTypeTable}.name) LIKE ?", ['%camion%'])
                    ->orWhereRaw("LOWER({$vehicleTypeTable}.name) LIKE ?", ['%camión%']),
                AcarreoHelper::VARIANT_JAULA => $variantScope
                    ->whereRaw("LOWER({$vehicleTypeTable}.name) LIKE ?", ['%jaula%']),
                default => $variantScope->whereRaw('1 = 0'),
            };
        });
    }
}
