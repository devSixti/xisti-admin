<?php

namespace App\Helpers;

use App\Support\VehicleDocumentRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Opciones de registro de vehículo para conductores (viajes + envíos con moto/carro/motoratón).
 */
class DriverVehicleHelper
{
    /** vehicle_services.id excluidos del registro (envíos dedicados, motoratón deshabilitado). */
    public const EXCLUDED_SERVICE_IDS = [4, 5];

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

        foreach ($rows as $row) {
            $serviceId = (int) $row->id;
            $mode = (string) ($row->service_mode ?? 'transport');

            if ($mode === 'expreso' && ! MobileFeatureFlagsHelper::isExpresoEnabled($general)) {
                continue;
            }

            $list[] = self::mapServiceRow($row, $langPrefix, $serviceIconUrl, $vehicleIconUrl, null);
        }

        $expanded = self::expandTransportVariants(self::sortRegistrationList($list));

        return self::sortRegistrationList(array_merge($expanded, self::bicicletaRegistrationRows(
            $langPrefix,
            $serviceIconUrl,
            $vehicleIconUrl
        )));
    }

    /**
     * Expand car/moto rows into XISTI matrix variants for driver registration.
     */
    private static function expandTransportVariants(array $list): array
    {
        $expanded = [];
        foreach ($list as $item) {
            $serviceId = (int) ($item['service_id'] ?? 0);
            if ($serviceId === 1) {
                foreach ([
                    XistiVehicleVariantHelper::CARRO_ECONOMICO,
                    XistiVehicleVariantHelper::CARRO_ECO,
                    XistiVehicleVariantHelper::CARRO_COMODO,
                ] as $variant) {
                    $row = $item;
                    $row['delivery_variant'] = $variant;
                    $row['service_name'] = XistiVehicleVariantHelper::labelFor($variant);
                    $row['vehicle_type_list'] = self::vehicleTypesForVariant($variant, $serviceId, $vehicleIconUrl);
                    $expanded[] = $row;
                }
                continue;
            }
            if ($serviceId === 3) {
                foreach ([
                    XistiVehicleVariantHelper::MOTO_BAJO,
                    XistiVehicleVariantHelper::MOTO_ALTO,
                ] as $variant) {
                    $row = $item;
                    $row['delivery_variant'] = $variant;
                    $row['service_name'] = XistiVehicleVariantHelper::labelFor($variant);
                    $row['vehicle_type_list'] = self::vehicleTypesForVariant($variant, $serviceId, $vehicleIconUrl);
                    $expanded[] = $row;
                }
                continue;
            }
            $expanded[] = $item;
        }

        return $expanded;
    }

    /**
     * Bicicleta is registered as courier variant (service 4) but listed in driver onboarding.
     *
     * @return list<array<string, mixed>>
     */
    private static function bicicletaRegistrationRows(
        string $langPrefix,
        string $serviceIconUrl,
        string $vehicleIconUrl
    ): array {
        if (! Schema::hasTable('vehicle_services')) {
            return [];
        }

        $courier = DB::table('vehicle_services')->where('id', 4)->where('status', 1)->first();
        if ($courier === null) {
            return [];
        }

        $row = self::mapServiceRow(
            $courier,
            $langPrefix,
            $serviceIconUrl,
            $vehicleIconUrl,
            XistiVehicleVariantHelper::BICICLETA
        );
        $row['service_name'] = XistiVehicleVariantHelper::labelFor(XistiVehicleVariantHelper::BICICLETA);
        $row['vehicle_type_list'] = self::vehicleTypesForVariant(
            XistiVehicleVariantHelper::BICICLETA,
            4,
            $vehicleIconUrl
        );
        $row['_sort_key'] = '00030';

        return [$row];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function vehicleTypesForVariant(string $variant, int $serviceId, string $vehicleIconUrl): array
    {
        $types = self::vehicleTypesForService($serviceId, $vehicleIconUrl);
        if ($variant === XistiVehicleVariantHelper::BICICLETA) {
            $bike = array_values(array_filter(
                $types,
                static fn (array $t) => stripos((string) ($t['vehicle_type_name'] ?? ''), 'bicicleta') !== false
            ));
            if ($bike !== []) {
                return $bike;
            }

            return self::vehicleTypesForService(3, $vehicleIconUrl);
        }

        if ($serviceId === 3) {
            return array_values(array_filter(
                $types,
                static fn (array $t) => stripos((string) ($t['vehicle_type_name'] ?? ''), 'bicicleta') === false
            ));
        }

        return $types;
    }

    public static function isDeliveryOnlyRegistration(?string $deliveryVariant, int $serviceId): bool
    {
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

        $iconName = (string) ($row->icon_name ?? '');
        $serviceIcon = $iconName !== ''
            ? $serviceIconUrl . '/' . $iconName . '?v=' . DeliveryVehicleHelper::ICON_CACHE_VERSION
            : '';

        $vehicleTypes = self::vehicleTypesForVariant(
            (string) ($deliveryVariant ?? ''),
            $serviceId,
            $vehicleIconUrl
        );

        $isDeliveryOnly = self::isDeliveryOnlyRegistration($deliveryVariant, $serviceId);
        $mode = (string) ($row->service_mode ?? 'transport');

        return [
            'service_id' => $serviceId,
            'registration_key' => self::registrationKeyFor($serviceId, $deliveryVariant),
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
            'requires_plate' => self::requiresPlate($serviceId, $deliveryVariant) ? 1 : 0,
            'requires_vehicle_photos' => self::requiresVehiclePhotos($serviceId, $deliveryVariant) ? 1 : 0,
            '_sort_key' => self::sortKeyFor($serviceId, $mode),
        ];
    }

    private static function registrationKeyFor(int $serviceId, ?string $deliveryVariant): string
    {
        $variant = strtolower(trim((string) $deliveryVariant));
        if ($variant === 'bicicleta' || $variant === 'bicycle' || $serviceId === 4) {
            return 'bicycle';
        }

        return match ($serviceId) {
            3 => 'moto',
            1 => 'carro',
            default => 'vehicle',
        };
    }

    private static function requiresPlate(int $serviceId, ?string $deliveryVariant): bool
    {
        return ! VehicleDocumentRules::isBicycleRegistration(null, $deliveryVariant)
            && $serviceId !== 4;
    }

    private static function requiresVehiclePhotos(int $serviceId, ?string $deliveryVariant): bool
    {
        return self::requiresPlate($serviceId, $deliveryVariant);
    }

    private static function vehicleTypesForService(int $serviceId, string $vehicleIconUrl): array
    {
        if (! Schema::hasTable('transport_vehicle_type')) {
            return [];
        }

        $types = DB::table('transport_vehicle_type')
            ->select('id as vehicle_type_id', 'name as vehicle_type_name', 'icon_name')
            ->where('service_id', $serviceId)
            ->where('status', 1)
            ->get();

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

    private static function sortKeyFor(int $serviceId, string $serviceMode = 'transport'): string
    {
        if ($serviceMode === 'expreso') {
            return '00040';
        }

        $order = [
            3 => '00050',
            1 => '00060',
        ];

        return $order[$serviceId] ?? sprintf('%05d', 80 + $serviceId);
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
