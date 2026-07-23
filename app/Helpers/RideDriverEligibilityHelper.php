<?php

namespace App\Helpers;

use App\Models\TransportRideBook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RideDriverEligibilityHelper
{
    public static function courierRowForRide(int $rideId): ?object
    {
        if (! Schema::hasTable('user_courier_service_details')) {
            return null;
        }

        return DB::table('user_courier_service_details')->where('ride_id', $rideId)->first();
    }

    public static function shouldPreserveRideVehicleServiceId(object $ride): bool
    {
        if ((int) ($ride->vehicle_service_id ?? 0) === 4) {
            return true;
        }

        $courier = self::courierRowForRide((int) $ride->id);
        $errandType = is_string($courier->errand_type ?? null) ? trim($courier->errand_type) : null;

        return in_array($errandType, [EncomiendaHelper::ERRAND_ENCOMIENDA, EncomiendaHelper::ERRAND_ACARREO], true);
    }

    public static function driverCanServePendingRide(object $driverDetails, int $rideId): bool
    {
        $driverUserId = (int) ($driverDetails->user_id ?? 0);
        if ($driverUserId <= 0 || $rideId <= 0) {
            return false;
        }

        $serviceSetting = DB::table('service_settings')->first();
        $rideExpiry = (int) ($serviceSetting->ride_expiry ?? 30);
        $expireDateTime = date('Y-m-d H:i:s', strtotime('-' . $rideExpiry . ' minutes'));

        $query = TransportRideBook::query()
            ->join('users', 'users.id', '=', 'user_ride_booking.user_id')
            ->leftJoin('user_courier_service_details', 'user_courier_service_details.ride_id', '=', 'user_ride_booking.id')
            ->join('vehicle_services', 'vehicle_services.id', '=', 'user_ride_booking.vehicle_service_id')
            ->where('user_ride_booking.id', $rideId)
            ->where('user_ride_booking.status', 0)
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->where(function ($assigned) use ($driverUserId) {
                $assigned->whereNull('user_ride_booking.driver_id')
                    ->orWhere('user_ride_booking.driver_id', 0)
                    ->orWhere('user_ride_booking.driver_id', $driverUserId);
            })
            ->where('user_ride_booking.ride_time_out', '>=', $expireDateTime);

        ServiceCatalogHelper::applyDriverAvailableRidesServiceFilter($query, $driverDetails);

        return $query->exists();
    }

    /**
     * Align push recipients with the same service rules used in driver ride polling.
     */
    public static function applyPushNotificationDriverFilter($query, int $rideId, int $serviceId): void
    {
        $courier = self::courierRowForRide($rideId);
        $errandType = is_string($courier->errand_type ?? null) ? trim($courier->errand_type) : null;

        if ($errandType === EncomiendaHelper::ERRAND_ENCOMIENDA) {
            ServiceCatalogHelper::applyDeliveryCapableDriverFilter($query);
            $query->whereRaw('COALESCE(transport_driver_details.accept_encomiendas, transport_driver_details.accept_delivery, 0) = 1');
            $requestedId = (int) ($courier->requested_vehicle_service_id ?? 0);
            if (DeliveryVehicleHelper::isValidRequestedVehicleServiceId($requestedId)) {
                $query->where('transport_vehicle_type.service_id', $requestedId);
            }

            return;
        }

        if ($errandType === EncomiendaHelper::ERRAND_ACARREO) {
            $acarreoServiceId = AcarreoVehicleHelper::acarreosServiceId();
            if ($acarreoServiceId > 0) {
                $query->where('transport_vehicle_type.service_id', $acarreoServiceId);
            }
            AcarreoVehicleHelper::applyVariantDriverFilter($query, $courier->acarreo_vehicle_variant ?? null);

            return;
        }

        $vehicleService = DB::table('vehicle_services')->where('id', $serviceId)->first();
        $isDeliveryRide = RideKindHelper::isDeliveryRide([
            'service_id' => $serviceId,
            'item_description' => (string) ($courier->item_description ?? ''),
            'recipient_name' => (string) ($courier->recipient_name ?? ''),
        ]) || (($vehicleService->service_mode ?? 'transport') === 'delivery');

        if ($isDeliveryRide) {
            ServiceCatalogHelper::applyDeliveryCapableDriverFilter($query);
            $requestedId = (int) ($courier->requested_vehicle_service_id ?? 0);
            if (DeliveryVehicleHelper::isValidRequestedVehicleServiceId($requestedId)) {
                $query->where('transport_vehicle_type.service_id', $requestedId);
            }

            return;
        }

        $query->where('transport_driver_details.accept_transport', 1);
        $query->where('vehicle_services.id', $serviceId);
    }
}
