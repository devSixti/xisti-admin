<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Colombia v1.0.2 — envíos por medio de transporte (moto, carro, etc.).
 */
class DeliveryVehicleHelper
{
    /** Bump when vehicle-service/*.png assets change (cache bust for mobile). */
    public const ICON_CACHE_VERSION = '1.0';

    /** vehicle_services.id values passengers may request for envíos */
    public const PASSENGER_DELIVERY_SERVICE_IDS = [1, 3, 5];

    public static function deliveryOptionsForApi(string $langPrefix = ''): array
    {
        if (!Schema::hasTable('vehicle_services')) {
            return [];
        }

        $iconBase = url('/assets/images/vehicle-service/');
        $rows = DB::table('vehicle_services')
            ->whereIn('id', self::PASSENGER_DELIVERY_SERVICE_IDS)
            ->where('status', 1)
            ->orderByRaw("CASE id WHEN 3 THEN 1 WHEN 1 THEN 2 WHEN 5 THEN 3 ELSE 99 END")
            ->get(['id', 'name', 'es_name', 'icon_name', 'service_mode']);

        $options = [];
        foreach ($rows as $row) {
            $serviceId = (int) $row->id;
            $label = self::colombiaDeliveryLabel($serviceId, $langPrefix, $row);
            $iconName = self::deliveryIconFileName($serviceId, (string) ($row->icon_name ?? ''));
            $entry = [
                'vehicle_service_id' => $serviceId,
                'label' => $label,
                'service_icon' => $iconName !== ''
                    ? $iconBase . '/' . $iconName . '?v=' . self::ICON_CACHE_VERSION
                    : '',
            ];
            if ($serviceId === 5) {
                $entry['delivery_variant'] = 'motoraton';
                $entry['label'] = ($langPrefix === '' || str_starts_with($langPrefix, 'es')) ? 'Motoratón' : 'Motoratón';
                $motoratonIcon = (string) ($row->icon_name ?? '');
                if ($motoratonIcon === '') {
                    $motoratonIcon = '27531520260705.png';
                }
                $entry['service_icon'] = $iconBase . '/' . $motoratonIcon . '?v=' . self::ICON_CACHE_VERSION;
            }
            $options[] = $entry;
        }

        return self::appendExtraDeliveryMedia($options, $langPrefix, $iconBase);
    }

    public static function isValidRequestedVehicleServiceId(?int $id): bool
    {
        if ($id === null || $id <= 0) {
            return false;
        }

        return in_array($id, self::PASSENGER_DELIVERY_SERVICE_IDS, true);
    }

    /**
     * Driver sees a delivery ride when their vehicle's transport service matches the passenger request.
     */
    public static function driverMatchesDeliveryRequest(int $driverVehicleTypeId, ?int $requestedVehicleServiceId): bool
    {
        if ($requestedVehicleServiceId === null || $requestedVehicleServiceId <= 0) {
            return true;
        }

        if (!Schema::hasTable('transport_vehicle_type')) {
            return true;
        }

        $driverServiceId = (int) DB::table('transport_vehicle_type')
            ->where('id', $driverVehicleTypeId)
            ->value('service_id');

        return $driverServiceId === $requestedVehicleServiceId;
    }

    public static function serviceSupportsPassengerToggle(int $vehicleServiceId): bool
    {
        return in_array($vehicleServiceId, self::PASSENGER_DELIVERY_SERVICE_IDS, true);
    }

    public static function passengerDisclaimer(string $language = 'es'): string
    {
        if ($language === 'es') {
            return 'Los envíos son entregas de paquetes, no transporte de pasajeros. '
                . 'El conductor elegido debe coincidir con el medio de transporte que selecciones.';
        }

        return 'Deliveries are package shipments, not passenger transport. '
            . 'Only drivers registered for your selected vehicle type will receive your request.';
    }

    private static function colombiaDeliveryLabel(int $serviceId, string $langPrefix, object $row): string
    {
        $nameField = $langPrefix . 'name';
        if ($langPrefix !== '' && !empty($row->{$nameField})) {
            return (string) $row->{$nameField};
        }
        if (!empty($row->es_name)) {
            return (string) $row->es_name;
        }

        return match ($serviceId) {
            1 => 'Carro',
            3 => 'Moto',
            default => (string) ($row->name ?? 'Envío'),
        };
    }

    /**
     * Envíos / Encomiendas: bicicleta adicional (motocarro = vehicle_service id 5).
     */
    private static function appendExtraDeliveryMedia(array $options, string $langPrefix, string $iconBase): array
    {
        $moto = collect($options)->firstWhere('vehicle_service_id', 3);
        if ($moto === null) {
            return $options;
        }

        $isEs = $langPrefix === '' || str_starts_with($langPrefix, 'es');
        $v = self::ICON_CACHE_VERSION;
        $options[] = [
            'vehicle_service_id' => 5,
            'delivery_variant' => 'motocarro',
            'label' => $isEs ? 'Motocarro' : 'Motocarro',
            'service_icon' => $iconBase . '/motocarro.png?v=' . $v,
        ];
        $options[] = [
            'vehicle_service_id' => 3,
            'delivery_variant' => 'bicycle',
            'label' => $isEs ? 'Bicicleta' : 'Bicycle',
            'service_icon' => $iconBase . '/bicycle.png?v=' . $v,
        ];

        return $options;
    }

    /** Motocarro solo en envíos; id 5 en viajes usa el PNG de Motoratón en BD (p. ej. 27531520260705.png). */
    private static function deliveryIconFileName(int $serviceId, string $dbIcon): string
    {
        return match ($serviceId) {
            5 => 'motocarro.png',
            default => $dbIcon,
        };
    }
}
