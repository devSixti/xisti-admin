<?php

namespace App\Helpers;

use App\Models\ServiceSettings;
use App\Models\VehicleCommissionRate;
use Illuminate\Support\Facades\Schema;

class VehicleCommissionHelper
{
    /**
     * @return array<int, array{variant_key: string, label: string, vehicle_service_id: int|null, sort_order: int}>
     */
    public static function defaultCatalog(): array
    {
        return [
            ['variant_key' => XistiVehicleVariantHelper::CARRO_ECONOMICO, 'label' => 'Carro económico', 'vehicle_service_id' => 1, 'sort_order' => 10],
            ['variant_key' => XistiVehicleVariantHelper::CARRO_ECO, 'label' => 'Carro eléctrico', 'vehicle_service_id' => 1, 'sort_order' => 20],
            ['variant_key' => XistiVehicleVariantHelper::CARRO_COMODO, 'label' => 'Carro cómodo', 'vehicle_service_id' => 1, 'sort_order' => 30],
            ['variant_key' => XistiVehicleVariantHelper::MOTO_BAJO, 'label' => 'Moto bajo cilindraje', 'vehicle_service_id' => 3, 'sort_order' => 40],
            ['variant_key' => XistiVehicleVariantHelper::MOTO_ALTO, 'label' => 'Moto alto cilindraje', 'vehicle_service_id' => 3, 'sort_order' => 50],
            ['variant_key' => XistiVehicleVariantHelper::MOTO_MEDIO, 'label' => 'Moto (envíos)', 'vehicle_service_id' => 3, 'sort_order' => 60],
            ['variant_key' => XistiVehicleVariantHelper::BICICLETA, 'label' => 'Bicicleta', 'vehicle_service_id' => 4, 'sort_order' => 70],
            ['variant_key' => 'courier', 'label' => 'Mensajero / Envío', 'vehicle_service_id' => 4, 'sort_order' => 80],
        ];
    }

    public static function globalDefaultPercent(): float
    {
        $service = ServiceSettings::query()->first();

        return (float) ($service->admin_commission ?? config('xisti.default_commission_percent', 8));
    }

    public static function resolvePercent(?int $vehicleServiceId = null, ?string $deliveryVariant = null): float
    {
        $default = self::globalDefaultPercent();

        if (! Schema::hasTable('vehicle_commission_rates')) {
            return $default;
        }

        $variant = strtolower(trim((string) $deliveryVariant));
        if ($variant !== '') {
            $rate = VehicleCommissionRate::query()
                ->where('variant_key', $variant)
                ->where('status', 1)
                ->first();
            if ($rate !== null) {
                return (float) $rate->commission_percent;
            }
        }

        return $default;
    }

    /**
     * @return array<string, float>
     */
    public static function ratesMapForMobile(): array
    {
        if (! Schema::hasTable('vehicle_commission_rates')) {
            return [];
        }

        $map = [];
        $rates = VehicleCommissionRate::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get(['variant_key', 'commission_percent']);

        foreach ($rates as $rate) {
            $map[$rate->variant_key] = (float) $rate->commission_percent;
        }

        if ($map === []) {
            $map['default'] = self::globalDefaultPercent();
        }

        return $map;
    }
}
