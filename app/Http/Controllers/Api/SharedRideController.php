<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SharedRideFareHelper;
use App\Helpers\SharedRideHelper;
use App\Helpers\XistiVehicleVariantHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SharedRideController extends Controller
{
    public function postCreateOffer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_kind' => 'required|in:' . implode(',', SharedRideHelper::allowedTripKinds()),
            'origin_town' => 'required|string|max:120',
            'destination_town' => 'required|string|max:120',
            'trip_date' => 'required|date|after_or_equal:today',
            'seats_total' => 'required|integer|min:1|max:8',
            'fare_per_person' => 'required|numeric|min:0|max:9999999',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $driverId = (int) $request->get('user_id');
        $driverDetails = DB::table('transport_driver_details')->where('user_id', $driverId)->first();
        if ($driverDetails === null) {
            return response()->json(['status' => 0, 'message' => 'Registro de socio conductor requerido.', 'message_code' => 0]);
        }

        $serviceId = (int) DB::table('transport_vehicle_type')
            ->where('id', $driverDetails->vehicle_type_id)
            ->value('service_id');
        $serviceMode = (string) DB::table('vehicle_services')->where('id', $serviceId)->value('service_mode');

        if (! SharedRideHelper::driverMayCreateOffer($serviceId, $serviceMode)) {
            return response()->json([
                'status' => 0,
                'message' => 'Solo socios con carro o viajes compartidos pueden publicar viajes.',
                'message_code' => 0,
            ]);
        }

        $seats = (int) $request->get('seats_total');
        $driverVariant = '';
        if (\Illuminate\Support\Facades\Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            $driverVariant = XistiVehicleVariantHelper::normalize(
                (string) DB::table('transport_driver_details')->where('user_id', $driverId)->value('delivery_variant')
            );
        }

        $offerRow = [
            'driver_id' => $driverId,
            'driver_vehicle_type_id' => (int) $driverDetails->vehicle_type_id,
            'trip_kind' => SharedRideHelper::normalizeTripKind($request->get('trip_kind')),
            'origin_town' => trim($request->get('origin_town')),
            'destination_town' => trim($request->get('destination_town')),
            'trip_date' => $request->get('trip_date'),
            'seats_total' => $seats,
            'seats_available' => $seats,
            'fare_per_person' => round((float) $request->get('fare_per_person'), 2),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('shared_ride_offers', 'vehicle_variant') && $driverVariant !== '') {
            $offerRow['vehicle_variant'] = $driverVariant;
        }
        $id = DB::table('shared_ride_offers')->insertGetId($offerRow);

        return response()->json([
            'status' => 1,
            'message' => 'Viaje compartido publicado.',
            'offer_id' => $id,
        ]);
    }

    public function postPassengerSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_kind' => 'required|in:' . implode(',', SharedRideHelper::allowedTripKinds()),
            'origin_town' => 'required|string|max:120',
            'destination_town' => 'required|string|max:120',
            'trip_date' => 'required|date',
            'vehicle_variant' => 'nullable|string|max:64',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        if (! Schema::hasTable('shared_ride_passenger_searches')) {
            return response()->json(['status' => 0, 'message' => 'Servicio no disponible.', 'message_code' => 0]);
        }

        $userId = (int) $request->get('user_id');
        $tripKind = SharedRideHelper::normalizeTripKind($request->get('trip_kind'));
        $origin = trim($request->get('origin_town'));
        $destination = trim($request->get('destination_town'));
        $tripDate = $request->get('trip_date');
        $vehicleVariant = XistiVehicleVariantHelper::normalize($request->get('vehicle_variant'));

        $searchRow = [
            'user_id' => $userId,
            'trip_kind' => $tripKind,
            'origin_town' => $origin,
            'destination_town' => $destination,
            'trip_date' => $tripDate,
            'status' => 'searching',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('shared_ride_passenger_searches', 'vehicle_variant') && $vehicleVariant !== '') {
            $searchRow['vehicle_variant'] = $vehicleVariant;
        }
        $searchId = DB::table('shared_ride_passenger_searches')->insertGetId($searchRow);

        $matches = SharedRideHelper::matchOffersForSearch(
            $tripKind,
            $origin,
            $destination,
            $tripDate,
            $vehicleVariant !== '' ? $vehicleVariant : null
        );

        return response()->json([
            'status' => 1,
            'message' => count($matches) > 0
                ? 'Encontramos viajes compatibles.'
                : 'Solicitud agendada. Te avisaremos cuando haya un socio conductor con cupo.',
            'search_id' => $searchId,
            'scheduled' => count($matches) === 0 ? 1 : 0,
            'matches' => $matches,
        ]);
    }

    public function postJoinOffer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|integer|min:1',
            'search_id' => 'nullable|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $result = SharedRideHelper::joinOffer(
            (int) $request->get('offer_id'),
            (int) $request->get('user_id'),
            $request->filled('search_id') ? (int) $request->get('search_id') : null
        );

        return response()->json($result);
    }

    public function postMyOffers(Request $request): JsonResponse
    {
        $driverId = (int) $request->get('user_id');
        if (! Schema::hasTable('shared_ride_offers')) {
            return response()->json(['status' => 1, 'offers' => []]);
        }

        $offers = DB::table('shared_ride_offers')
            ->where('driver_id', $driverId)
            ->orderByDesc('trip_date')
            ->limit(50)
            ->get();

        return response()->json(['status' => 1, 'offers' => $offers]);
    }

    public function postFareEstimate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_town' => 'required|string|max:120',
            'destination_town' => 'required|string|max:120',
            'seats_total' => 'required|integer|min:1|max:8',
            'vehicle_variant' => 'nullable|string|max:64',
            'is_weekend' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $driverId = (int) $request->get('user_id');
        $variant = XistiVehicleVariantHelper::normalize($request->get('vehicle_variant'));
        if ($variant === '' && Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            $variant = XistiVehicleVariantHelper::normalize(
                (string) DB::table('transport_driver_details')->where('user_id', $driverId)->value('delivery_variant')
            );
        }

        $estimate = SharedRideFareHelper::recommendPerPerson(
            trim($request->get('origin_town')),
            trim($request->get('destination_town')),
            $variant,
            (int) $request->get('seats_total'),
            (bool) $request->boolean('is_weekend')
        );

        return response()->json([
            'status' => 1,
            'message' => 'Tarifa sugerida calculada.',
            'vehicle_variant' => $variant,
            'vehicle_label' => XistiVehicleVariantHelper::labelFor($variant),
            'estimate' => $estimate,
        ]);
    }
}
