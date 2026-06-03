<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * XISTI — medios activos para pasajero: Moto (3) y Carro (1) únicamente.
 */
class DeliveryVehicleHelper
{
    /** Bump when vehicle-service/*.png assets change (cache bust for mobile). */
    public const ICON_CACHE_VERSION = '1.2';

    /** vehicle_services.id — únicos medios habilitados (viajes, envíos, encomiendas). */
    public const PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS = [1, 3];

    /** @deprecated Use PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS */
    public const PASSENGER_DELIVERY_SERVICE_IDS = self::PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS;

    public static function isPassengerActiveVehicleServiceId(?int $id): bool
    {
        if ($id === null || $id <= 0) {
            return false;
        }

        return in_array($id, self::PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS, true);
    }

    public static function deliveryOptionsForApi(string $langPrefix = ''): array
    {
        if (! Schema::hasTable('vehicle_services')) {
            return [];
        }

        $iconBase = url('/assets/images/vehicle-service/');
        $rows = DB::table('vehicle_services')
            ->whereIn('id', self::PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS)
            ->where('status', 1)
            ->orderByRaw('CASE id WHEN 3 THEN 1 WHEN 1 THEN 2 ELSE 99 END')
            ->get(['id', 'name', 'es_name', 'icon_name', 'service_mode']);

        $options = [];
        foreach ($rows as $row) {
            $serviceId = (int) $row->id;
            $label = self::colombiaDeliveryLabel($serviceId, $langPrefix, $row);
            $iconName = (string) ($row->icon_name ?? '');
            $options[] = [
                'vehicle_service_id' => $serviceId,
                'label' => $label,
                'service_icon' => $iconName !== ''
                    ? $iconBase . '/' . $iconName . '?v=' . self::ICON_CACHE_VERSION
                    : '',
            ];
        }

        return $options;
    }

    /**
     * Oculta Motoratón (id 5) y otros transport legacy del home / service_modes.
     *
     * @param  array<int, array<string, mixed>>  $services
     * @return array<int, array<string, mixed>>
     */
    public static function filterHomeServiceRows(array $services): array
    {
        return array_values(array_filter($services, static function (array $row): bool {
            $id = (int) ($row['service_id'] ?? 0);
            if ($id === 5) {
                return false;
            }
            $mode = (string) ($row['service_mode'] ?? 'transport');
            if ($mode === 'transport') {
                return self::isPassengerActiveVehicleServiceId($id);
            }

            return true;
        }));
    }

    public static function isValidRequestedVehicleServiceId(?int $id): bool
    {
        return self::isPassengerActiveVehicleServiceId($id);
    }

    /**
     * Driver sees a delivery ride when their vehicle's transport service matches the passenger request.
     */
    public static function driverMatchesDeliveryRequest(int $driverVehicleTypeId, ?int $requestedVehicleServiceId): bool
    {
        if ($requestedVehicleServiceId === null || $requestedVehicleServiceId <= 0) {
            return true;
        }

        if (! Schema::hasTable('transport_vehicle_type')) {
            return true;
        }

        $driverServiceId = (int) DB::table('transport_vehicle_type')
            ->where('id', $driverVehicleTypeId)
            ->value('service_id');

        return $driverServiceId === $requestedVehicleServiceId;
    }

    public static function serviceSupportsPassengerToggle(int $vehicleServiceId): bool
    {
        return self::isPassengerActiveVehicleServiceId($vehicleServiceId);
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
        if ($langPrefix !== '' && ! empty($row->{$nameField})) {
            return (string) $row->{$nameField};
        }
        if (! empty($row->es_name)) {
            return (string) $row->es_name;
        }

        return match ($serviceId) {
            1 => 'Carro',
            3 => 'Moto',
            default => (string) ($row->name ?? 'Envío'),
        };
    }
}
