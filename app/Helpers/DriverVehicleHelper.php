<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Colombia v1.0.2 — opciones de registro de vehículo para conductores.
 * Sustituye Envíos / Encomiendas por Motocarro y Bicicleta (medios de transporte).
 */
class DriverVehicleHelper
{
    /** vehicle_services.id excluidos del registro (modos pasajero, no medio de transporte). */
    public const EXCLUDED_SERVICE_IDS = [4];

    public const EXCLUDED_SERVICE_MODES = ['delivery', 'encomiendas'];

    public static function registrationServiceList(
        string $langPrefix,
        string $serviceIconUrl,
        string $vehicleIconUrl
    ): array {
        if (! Schema::hasTable('vehicle_services')) {
            return [];
        }

        $select = ['id', 'name', 'es_name', 'icon_name', 'service_mode', 'vehicle_service_description', 'display_order'];
        if ($langPrefix !== '') {
            $select[] = $langPrefix . 'name';
        }
        $rows = DB::table('vehicle_services')
            ->where('status', 1)
            ->whereNotIn('id', self::EXCLUDED_SERVICE_IDS)
            ->whereNotIn('service_mode', self::EXCLUDED_SERVICE_MODES)
            ->orderBy('display_order')
            ->get($select);

        $general = request()->get('general_settings');
        $list = [];
        $hasMoto = false;
        $hasMotoraton = false;

        foreach ($rows as $row) {
            $serviceId = (int) $row->id;
            $mode = (string) ($row->service_mode ?? 'transport');

            if ($mode === 'expreso' && ! MobileFeatureFlagsHelper::isExpresoEnabled($general)) {
                continue;
            }

            if ($serviceId === 3) {
                $hasMoto = true;
            }
            if ($serviceId === 5) {
                $hasMotoraton = true;
                $row = clone $row;
                $icon = (string) ($row->icon_name ?? '');
                if ($icon === '' || $icon === 'motocarro.png') {
                    $row->icon_name = '27531520260705.png';
                }
            }

            $list[] = self::mapServiceRow($row, $langPrefix, $serviceIconUrl, $vehicleIconUrl, null);
        }

        if ($hasMotoraton) {
            $list[] = self::syntheticDeliveryService(
                serviceId: 5,
                variant: 'motocarro',
                labelEs: 'Motocarro',
                labelEn: 'Motocarro',
                iconFile: 'motocarro.png',
                langPrefix: $langPrefix,
                serviceIconUrl: $serviceIconUrl,
                vehicleIconUrl: $vehicleIconUrl,
            );
        }

        if ($hasMoto) {
            $list[] = self::syntheticDeliveryService(
                serviceId: 3,
                variant: 'bicycle',
                labelEs: 'Bicicleta',
                labelEn: 'Bicycle',
                iconFile: 'bicycle.png',
                langPrefix: $langPrefix,
                serviceIconUrl: $serviceIconUrl,
                vehicleIconUrl: $vehicleIconUrl,
            );
        }

        return self::sortRegistrationList($list);
    }

    public static function isDeliveryOnlyRegistration(?string $deliveryVariant, int $serviceId): bool
    {
        if ($deliveryVariant === 'motocarro' || $deliveryVariant === 'bicycle') {
            return true;
        }

        return $serviceId === 4;
    }

    /**
     * @param  object  $row  vehicle_services row
     */
    private static function mapServiceRow(
        object $row,
        string $langPrefix,
        string $serviceIconUrl,
        string $vehicleIconUrl,
        ?string $deliveryVariant
    ): array {
        $serviceId = (int) $row->id;
        $nameField = $langPrefix . 'name';
        $serviceName = '';
        if ($langPrefix !== '' && ! empty($row->{$nameField})) {
            $serviceName = (string) $row->{$nameField};
        } elseif (! empty($row->es_name)) {
            $serviceName = (string) $row->es_name;
        } else {
            $serviceName = (string) ($row->name ?? '');
        }

        if ($serviceId === 5 && ($deliveryVariant === null || $deliveryVariant === 'motoraton')) {
            $serviceName = ($langPrefix === '' || str_starts_with($langPrefix, 'es')) ? 'Motoratón' : 'Motoratón';
        }

        $iconName = (string) ($row->icon_name ?? '');
        $serviceIcon = $iconName !== ''
            ? $serviceIconUrl . '/' . $iconName . '?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION
            : '';

        $vehicleTypes = self::vehicleTypesForService($serviceId, $vehicleIconUrl, $deliveryVariant);

        $isDeliveryOnly = self::isDeliveryOnlyRegistration($deliveryVariant, $serviceId);
        $mode = (string) ($row->service_mode ?? 'transport');

        return [
            'service_id' => $serviceId,
            'service_name' => $serviceName,
            'service_icon' => $serviceIcon,
            'service_description' => (string) ($row->vehicle_service_description ?? ''),
            'vehicle_type_list' => $vehicleTypes,
            'delivery_variant' => $deliveryVariant ?? '',
            'supports_passenger_transport_toggle' => ! $isDeliveryOnly
                && DeliveryVehicleHelper::serviceSupportsPassengerToggle($serviceId)
                && $mode === 'transport',
            'is_delivery_only_service' => $isDeliveryOnly ? 1 : 0,
            'requires_technical_inspection' => ($serviceId === 1 && $mode === 'transport') ? 1 : 0,
            '_sort_key' => self::sortKeyFor($serviceId, $deliveryVariant, $mode),
        ];
    }

    private static function syntheticDeliveryService(
        int $serviceId,
        string $variant,
        string $labelEs,
        string $labelEn,
        string $iconFile,
        string $langPrefix,
        string $serviceIconUrl,
        string $vehicleIconUrl
    ): array {
        $isEs = $langPrefix === '' || str_starts_with($langPrefix, 'es');
        $label = $isEs ? $labelEs : $labelEn;
        $icon = $serviceIconUrl . '/' . $iconFile . '?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION;

        return [
            'service_id' => $serviceId,
            'service_name' => $label,
            'service_icon' => $icon,
            'service_description' => $isEs
                ? 'Entregas de paquetes con ' . strtolower($label) . '. No transporte de pasajeros.'
                : 'Package deliveries with ' . strtolower($label) . '. Not for passengers.',
            'vehicle_type_list' => self::vehicleTypesForService($serviceId, $vehicleIconUrl, $variant),
            'delivery_variant' => $variant,
            'supports_passenger_transport_toggle' => false,
            'is_delivery_only_service' => 1,
            'requires_technical_inspection' => 0,
            '_sort_key' => self::sortKeyFor($serviceId, $variant, 'transport'),
        ];
    }

    private static function vehicleTypesForService(int $serviceId, string $vehicleIconUrl, ?string $deliveryVariant): array
    {
        if (! Schema::hasTable('transport_vehicle_type')) {
            return [];
        }

        $query = DB::table('transport_vehicle_type')
            ->select('id as vehicle_type_id', 'name as vehicle_type_name', 'icon_name')
            ->where('service_id', $serviceId)
            ->where('status', 1);

        if ($deliveryVariant === 'bicycle') {
            $query->where(function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%bicicleta%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%bicycle%']);
            });
        }

        $types = $query->get();

        if ($deliveryVariant === 'bicycle' && $types->isEmpty()) {
            return [[
                'vehicle_type_id' => self::defaultBicycleVehicleTypeId($serviceId),
                'vehicle_type_name' => 'Bicicleta',
                'vehicle_icon' => '',
            ]];
        }

        $out = [];
        foreach ($types as $type) {
            $icon = (string) ($type->icon_name ?? '');
            $out[] = [
                'vehicle_type_id' => (int) $type->vehicle_type_id,
                'vehicle_type_name' => (string) $type->vehicle_type_name,
                'vehicle_icon' => $icon !== ''
                    ? $vehicleIconUrl . '/' . $icon . '?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION
                    : '',
            ];
        }

        return $out;
    }

    private static function defaultBicycleVehicleTypeId(int $serviceId): int
    {
        if (! Schema::hasTable('transport_vehicle_type')) {
            return 0;
        }

        $id = DB::table('transport_vehicle_type')
            ->where('service_id', $serviceId)
            ->where('status', 1)
            ->whereRaw('LOWER(name) LIKE ?', ['%bicicleta%'])
            ->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) (DB::table('transport_vehicle_type')
            ->where('service_id', $serviceId)
            ->where('status', 1)
            ->orderBy('id')
            ->value('id') ?? 0);
    }

    private static function sortKeyFor(int $serviceId, ?string $variant, string $serviceMode = 'transport'): string
    {
        if ($serviceMode === 'expreso') {
            return '00040';
        }

        $key = $serviceId . ':' . ($variant ?? '');
        $order = [
            '5:' => '00010',
            '5:motocarro' => '00020',
            '3:bicycle' => '00030',
            '3:' => '00050',
            '1:' => '00060',
        ];

        return $order[$key] ?? sprintf('%05d', 80 + $serviceId);
    }

    private static function sortRegistrationList(array $list): array
    {
        usort($list, static fn ($a, $b) => strcmp($a['_sort_key'] ?? '', $b['_sort_key'] ?? ''));
        foreach ($list as &$item) {
            unset($item['_sort_key']);
        }

        return $list;
    }
}
