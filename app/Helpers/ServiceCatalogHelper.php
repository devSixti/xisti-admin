<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceCatalogHelper
{
    public static function homeServiceSelect(string $langPrefix, float $currencyRatio): array
    {
        $iconBase = url('/assets/images/vehicle-service/');
        return [
            'id as service_id',
            $langPrefix . 'name as service_name',
            'max_bargain_percent as min_offer_fare_amount',
            'service_mode',
            'display_order',
            DB::raw("(CASE WHEN icon_name != '' THEN (concat('$iconBase','/',icon_name,'?v=" . DeliveryVehicleHelper::ICON_CACHE_VERSION . "')) ELSE '' END) as service_icon"),
            DB::raw('ROUND(cost_for_km * ' . $currencyRatio . ',2) As cost_for_km'),
            DB::raw("(CASE WHEN vehicle_service_description IS NOT NULL THEN vehicle_service_description ELSE '' END) as service_description"),
        ];
    }

    public static function buildServiceModesFromRows(array $services, string $language = 'es'): array
    {
        $labels = [
            'transport' => $language === 'es' ? 'Viajes' : 'Rides',
            'delivery' => $language === 'es' ? 'Entregas' : 'Deliveries',
            'expreso' => $language === 'es' ? 'Compartido' : 'Shared',
            'viajes_compartidos' => $language === 'es' ? 'Compartido' : 'Shared',
            'encomiendas' => $language === 'es' ? 'Encomiendas' : 'Errands',
            'acarreos' => $language === 'es' ? 'Carga' : 'Freight',
            'carga' => $language === 'es' ? 'Carga' : 'Freight',
        ];
        $grouped = [];
        foreach ($services as $row) {
            $mode = $row['service_mode'] ?? 'transport';
            if ($mode === 'encomiendas') {
                continue;
            }
            if (!isset($grouped[$mode])) {
                $grouped[$mode] = [];
            }
            $grouped[$mode][] = $row;
        }
        $modeOrder = ['transport', 'delivery', 'expreso', 'viajes_compartidos', 'acarreos', 'carga'];
        $modes = [];
        $order = 1;
        foreach ($modeOrder as $mode) {
            if (empty($grouped[$mode])) {
                continue;
            }
            usort($grouped[$mode], fn ($a, $b) => ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0));
            $modes[] = [
                'mode' => $mode,
                'label' => $labels[$mode] ?? ucfirst($mode),
                'display_order' => $order++,
                'services' => array_values($grouped[$mode]),
            ];
        }
        foreach ($grouped as $mode => $rows) {
            if (in_array($mode, $modeOrder, true)) {
                continue;
            }
            usort($rows, fn ($a, $b) => ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0));
            $modes[] = [
                'mode' => $mode,
                'label' => $labels[$mode] ?? ucfirst($mode),
                'display_order' => $order++,
                'services' => array_values($rows),
            ];
        }
        return MobileFeatureFlagsHelper::filterServiceModes($modes);
    }

    public static function eligibleServiceIdsForVehicleType(int $vehicleTypeId, int $fallbackServiceId): array
    {
        $ids = [];
        if (Schema::hasTable('vehicle_type_service_eligibility')) {
            $ids = DB::table('vehicle_type_service_eligibility')
                ->where('vehicle_type_id', $vehicleTypeId)
                ->pluck('service_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $merged = array_values(array_unique(array_filter(
            array_merge($ids, [$fallbackServiceId]),
            static fn ($id) => $id > 0
        )));

        return $merged !== [] ? $merged : [$fallbackServiceId];
    }

    /** Transport services (moto, carro) may receive envíos by default. */
    public const DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS = [1, 3];

    public static function driverCanReceiveDelivery(int $vehicleTypeId, int $fallbackServiceId): bool
    {
        if (in_array($fallbackServiceId, self::DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS, true)) {
            return true;
        }

        return in_array(4, self::eligibleServiceIdsForVehicleType($vehicleTypeId, $fallbackServiceId), true);
    }

    /**
     * Scope for drivers who can receive push / poll for envíos (service 4).
     */
    public static function applyDeliveryCapableDriverFilter($query, string $driverDetailsTable = 'transport_driver_details'): void
    {
        $query->where(function ($outer) use ($driverDetailsTable) {
            $outer->where("{$driverDetailsTable}.accept_delivery", 1);
            $outer->orWhereIn('transport_vehicle_type.service_id', self::DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS);
            if (Schema::hasTable('vehicle_type_service_eligibility')) {
                $outer->orWhereExists(function ($sub) use ($driverDetailsTable) {
                    $sub->select(DB::raw(1))
                        ->from('vehicle_type_service_eligibility as vtse')
                        ->whereColumn('vtse.vehicle_type_id', "{$driverDetailsTable}.vehicle_type_id")
                        ->where('vtse.service_id', 4);
                });
            }
        });
    }

    /**
     * Available-ride list: transport services from eligibility + envíos (service 4) when allowed.
     * Envíos sin requested_vehicle_service_id coinciden con cualquier conductor de reparto elegible.
     */
    public static function applyDriverAvailableRidesServiceFilter($query, object $driverDetails)
    {
        $vehicleTypeId = (int) ($driverDetails->vehicle_type_id ?? 0);
        $driverTransportServiceId = (int) ($driverDetails->service_id ?? 0);
        $eligibleIds = self::eligibleServiceIdsForVehicleType($vehicleTypeId, $driverTransportServiceId);
        $transportIds = array_values(array_filter($eligibleIds, static fn ($id) => (int) $id !== 4));
        $deliveryCapable = self::driverCanReceiveDelivery($vehicleTypeId, $driverTransportServiceId);
        $canReceiveDelivery = $deliveryCapable && (int) ($driverDetails->accept_delivery ?? 0) === 1;
        $acceptEncomiendas = (int) ($driverDetails->accept_encomiendas ?? ($driverDetails->accept_delivery ?? 0)) === 1;
        $canReceiveEncomiendas = $deliveryCapable && $acceptEncomiendas;

        $hasErrandType = Schema::hasColumn('user_courier_service_details', 'errand_type');
        $driverVariant = \App\Helpers\XistiVehicleVariantHelper::normalize($driverDetails->delivery_variant ?? '');

        return $query->where(function ($outer) use ($transportIds, $canReceiveDelivery, $canReceiveEncomiendas, $driverTransportServiceId, $hasErrandType, $driverVariant) {
            if ($transportIds !== []) {
                $outer->where(function ($transportQuery) use ($transportIds, $hasErrandType, $driverVariant) {
                    $transportQuery->where('user_ride_booking.vehicle_service_id', '!=', 4)
                        ->whereIn('user_ride_booking.vehicle_service_id', $transportIds);
                    \App\Helpers\XistiVehicleVariantHelper::applyTransportVariantRideFilter($transportQuery, $driverVariant);
                    if ($hasErrandType) {
                        $transportQuery->where(function ($excludeEncomienda) {
                            $excludeEncomienda->whereNull('user_courier_service_details.errand_type')
                                ->orWhere('user_courier_service_details.errand_type', '!=', EncomiendaHelper::ERRAND_ENCOMIENDA);
                        });
                    }
                });
            }
            if ($canReceiveDelivery || $canReceiveEncomiendas) {
                $method = $transportIds !== [] ? 'orWhere' : 'where';
                $outer->{$method}(function ($deliveryQuery) use ($canReceiveDelivery, $canReceiveEncomiendas, $driverTransportServiceId, $hasErrandType) {
                    $addedAny = false;
                    if ($canReceiveDelivery) {
                        $deliveryQuery->where(function ($legacyDelivery) use ($driverTransportServiceId, $hasErrandType, $driverVariant) {
                            $legacyDelivery->where('user_ride_booking.vehicle_service_id', 4)
                                ->where(function ($matchQuery) use ($driverTransportServiceId) {
                                    if (Schema::hasColumn('user_courier_service_details', 'requested_vehicle_service_id')) {
                                        $matchQuery->whereNull('user_courier_service_details.requested_vehicle_service_id')
                                            ->orWhere('user_courier_service_details.requested_vehicle_service_id', $driverTransportServiceId);
                                    }
                                });
                            if ($hasErrandType) {
                                $legacyDelivery->where(function ($deliveryOnly) {
                                    $deliveryOnly->whereNull('user_courier_service_details.errand_type')
                                        ->orWhere('user_courier_service_details.errand_type', EncomiendaHelper::ERRAND_DELIVERY);
                                });
                            }
                            \App\Helpers\XistiVehicleVariantHelper::applyTransportVariantRideFilter($legacyDelivery, $driverVariant);
                        });
                        $addedAny = true;
                    }
                    if ($canReceiveEncomiendas && $hasErrandType) {
                        $method = $addedAny ? 'orWhere' : 'where';
                        $deliveryQuery->{$method}(function ($encomiendaQuery) use ($driverTransportServiceId, $driverVariant) {
                            $encomiendaQuery->where('user_courier_service_details.errand_type', EncomiendaHelper::ERRAND_ENCOMIENDA)
                                ->where(function ($matchQuery) use ($driverTransportServiceId) {
                                    if (Schema::hasColumn('user_courier_service_details', 'requested_vehicle_service_id')) {
                                        $matchQuery->whereNull('user_courier_service_details.requested_vehicle_service_id')
                                            ->orWhere('user_courier_service_details.requested_vehicle_service_id', $driverTransportServiceId);
                                    }
                                });
                            \App\Helpers\XistiVehicleVariantHelper::applyTransportVariantRideFilter($encomiendaQuery, $driverVariant);
                        });
                    }
                });
            }
        });
    }
}
