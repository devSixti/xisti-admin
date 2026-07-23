<?php

namespace App\Helpers;

use App\Helpers\FcmPushHelper;
use App\Models\TransportRideBook;
use App\Models\User;
use App\Models\WorldCurrency;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SharedRideHelper
{
    public const MODE = 'viajes_compartidos';

    public const KIND_PUEBLO_PUEBLO = 'pueblo_a_pueblo';

    public const KIND_PUEBLO_CIUDAD = 'pueblo_a_ciudad';

    public const DATE_WINDOW_DAYS = 7;

    public static function allowedTripKinds(): array
    {
        return [self::KIND_PUEBLO_PUEBLO, self::KIND_PUEBLO_CIUDAD];
    }

    public static function normalizeTripKind(?string $kind): ?string
    {
        $key = strtolower(trim((string) $kind));

        return in_array($key, self::allowedTripKinds(), true) ? $key : null;
    }

    /** Prefer official DIVIPOLA municipality name when a match exists. */
    public static function normalizeTownName(string $town): string
    {
        $town = trim($town);
        if ($town === '') {
            return $town;
        }
        try {
            $hit = MunicipioResolveHelper::matchByName($town);
            if ($hit !== null && ! empty($hit['name'])) {
                return (string) $hit['name'];
            }
        } catch (\Throwable $e) {
            // Table may not exist yet during migrations.
        }

        return $town;
    }

    public static function passengerDisclaimer(string $language = 'es'): string
    {
        if ($language === 'es') {
            return 'Consiste en viaje compartido intermunicipal.';
        }

        return 'This is a shared ride with other passengers on intercity routes. '
            . 'Enter origin, destination, and trip date.';
    }

    public static function driverMayCreateOffer(int $driverServiceId, ?string $serviceMode): bool
    {
        $mode = strtolower((string) $serviceMode);

        return $driverServiceId === 1 || $mode === self::MODE;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function matchOffersForSearch(
        string $tripKind,
        string $originTown,
        string $destinationTown,
        string $tripDate
    ): array {
        if (! Schema::hasTable('shared_ride_offers')) {
            return [];
        }

        $date = Carbon::parse($tripDate);
        $from = $date->copy()->subDays(self::DATE_WINDOW_DAYS)->toDateString();
        $to = $date->copy()->addDays(self::DATE_WINDOW_DAYS)->toDateString();

        $originTown = self::normalizeTownName($originTown);
        $destinationTown = self::normalizeTownName($destinationTown);

        $rows = DB::table('shared_ride_offers')
            ->where('status', 'open')
            ->where('trip_kind', $tripKind)
            ->whereRaw('LOWER(TRIM(origin_town)) = ?', [strtolower(trim($originTown))])
            ->whereRaw('LOWER(TRIM(destination_town)) = ?', [strtolower(trim($destinationTown))])
            ->whereBetween('trip_date', [$from, $to])
            ->where('seats_available', '>', 0)
            ->orderByRaw('ABS(DATEDIFF(trip_date, ?))', [$date->toDateString()])
            ->limit(30)
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $driver = User::query()->find((int) $row->driver_id);
            $out[] = [
                'offer_id' => (int) $row->id,
                'driver_id' => (int) $row->driver_id,
                'driver_name' => $driver ? trim($driver->first_name . ' ' . ($driver->last_name ?? '')) : '',
                'driver_profile_image' => $driver->profile_image ?? '',
                'trip_kind' => $row->trip_kind,
                'origin_town' => $row->origin_town,
                'destination_town' => $row->destination_town,
                'trip_date' => $row->trip_date,
                'seats_available' => (int) $row->seats_available,
                'seats_total' => (int) $row->seats_total,
                'fare_per_person' => (float) ($row->fare_per_person ?? 0),
            ];
        }

        return $out;
    }

    public static function joinOffer(
        int $offerId,
        int $userId,
        ?int $searchId,
    ): array {
        return DB::transaction(function () use ($offerId, $userId, $searchId) {
            $offer = DB::table('shared_ride_offers')->where('id', $offerId)->lockForUpdate()->first();
            if ($offer === null || $offer->status !== 'open' || (int) $offer->seats_available < 1) {
                return ['status' => 0, 'message' => 'No hay cupos disponibles en este viaje.'];
            }

            $exists = DB::table('shared_ride_members')
                ->where('offer_id', $offerId)
                ->where('user_id', $userId)
                ->exists();
            if ($exists) {
                return ['status' => 0, 'message' => 'Ya estás inscrito en este viaje compartido.'];
            }

            $passenger = User::query()->find($userId);
            $driver = User::query()->find((int) $offer->driver_id);
            if ($passenger === null || $driver === null) {
                return ['status' => 0, 'message' => 'Usuario no encontrado.'];
            }

            $rideId = self::createChatRideForSharedTrip($offer, $passenger, $driver);

            DB::table('shared_ride_members')->insert([
                'offer_id' => $offerId,
                'user_id' => $userId,
                'search_id' => $searchId,
                'ride_id' => $rideId,
                'status' => 'joined',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $newAvailable = max(0, (int) $offer->seats_available - 1);
            DB::table('shared_ride_offers')->where('id', $offerId)->update([
                'seats_available' => $newAvailable,
                'status' => $newAvailable > 0 ? 'open' : 'full',
                'updated_at' => now(),
            ]);

            if ($searchId !== null) {
                DB::table('shared_ride_passenger_searches')->where('id', $searchId)->update([
                    'status' => 'matched',
                    'updated_at' => now(),
                ]);
            }

            self::notifyDriverOfPassengerJoin($driver, $passenger, $offer, $rideId);
            self::notifyPassengerOfJoinPending($passenger, $offer, $rideId);

            return [
                'status' => 1,
                'message' => 'Te uniste al viaje compartido. El socio conductor debe confirmar tu cupo.',
                'ride_id' => $rideId,
                'offer_id' => $offerId,
            ];
        });
    }

    private static function notifyDriverOfPassengerJoin(User $driver, User $passenger, object $offer, int $rideId): void
    {
        $token = trim((string) ($driver->device_token ?? ''));
        if ($token === '') {
            return;
        }

        $lang = filled($driver->language) && $driver->language !== 'Null' ? (string) $driver->language : 'es';
        $driverCurrency = \App\Support\UserCurrencyResolver::forCurrency($driver->currency);
        if ($driverCurrency === null) {
            $driverCurrency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currencySymbol = (string) ($driverCurrency->symbol ?? '$');
        $currencyRatio = (float) ($driverCurrency->ratio ?? 1);
        $displayPrice = round((float) ($offer->fare_per_person ?? 0) * $currencyRatio, 2);

        $passengerName = trim($passenger->first_name . ' ' . ($passenger->last_name ?? ''));
        $routeLabel = $offer->origin_town . ' → ' . $offer->destination_town;

        $event = PushEventTemplateHelper::resolve('driver_new_request', $lang, [
            'currency' => $currencySymbol,
            'price' => (string) $displayPrice,
            'pickup' => (string) $offer->origin_town,
            'destination' => (string) $offer->destination_town,
        ]);

        $title = $event['title'] !== ''
            ? $event['title']
            : 'Nuevo pasajero en tu viaje compartido';
        $message = $event['message'] !== ''
            ? $event['message']
            : ($passengerName !== '' ? $passengerName . ' — ' : '') . $routeLabel;

        FcmPushHelper::sendToTokenForLoginDevice(
            $token,
            $title,
            $message,
            [
                'title' => $title,
                'title_code' => (string) ($event['title_code'] ?? 91),
                'sound' => 'true',
                'notification_type' => (string) ($event['notification_type'] ?? 7),
                'user_type' => '2',
                'ride_status' => '0',
                'ride_type' => '1',
                'message' => $message,
                'body' => $message,
                'message_code' => (string) ($event['message_code'] ?? 90),
                'ride_id' => (string) $rideId,
                'pickup_address' => (string) $offer->origin_town,
                'destination_address' => (string) $offer->destination_town,
                'offered_price' => (string) $displayPrice,
                'customer_name' => $passengerName,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'dispatch_action' => 'refresh_available_rides',
                'dispatch_ts' => (string) time(),
            ],
            $event['sound'] ?? 'new_request.wav',
            (int) ($driver->login_device ?? 0) > 0 ? (int) $driver->login_device : null
        );
    }

    private static function notifyPassengerOfJoinPending(User $passenger, object $offer, int $rideId): void
    {
        $token = trim((string) ($passenger->device_token ?? ''));
        if ($token === '') {
            return;
        }

        $lang = filled($passenger->language) && $passenger->language !== 'Null' ? (string) $passenger->language : 'es';
        $event = PushEventTemplateHelper::resolve('passenger_shared_ride_join_pending', $lang, [
            'origin' => (string) $offer->origin_town,
            'destination' => (string) $offer->destination_town,
        ]);

        $title = $event['title'] !== '' ? $event['title'] : 'Cupo enviado';
        $message = $event['message'] !== ''
            ? $event['message']
            : 'Tu solicitud de cupo fue enviada al socio conductor. Te avisaremos cuando confirme.';

        FcmPushHelper::sendToTokenForLoginDevice(
            $token,
            $title,
            $message,
            [
                'title' => $title,
                'title_code' => (string) ($event['title_code'] ?? 150),
                'sound' => 'true',
                'notification_type' => (string) ($event['notification_type'] ?? 1),
                'user_type' => '1',
                'ride_status' => '0',
                'ride_type' => '1',
                'message' => $message,
                'body' => $message,
                'message_code' => (string) ($event['message_code'] ?? 21),
                'ride_id' => (string) $rideId,
                'pickup_address' => (string) $offer->origin_town,
                'destination_address' => (string) $offer->destination_town,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'dispatch_action' => 'refresh_ride_status',
                'dispatch_ts' => (string) time(),
            ],
            $event['sound'] ?? 'default',
            (int) ($passenger->login_device ?? 0) > 0 ? (int) $passenger->login_device : null
        );
    }

    private static function resolveVehicleServiceId(object $offer): int
    {
        $vehicleTypeId = (int) ($offer->driver_vehicle_type_id ?? 0);
        if ($vehicleTypeId > 0) {
            $serviceId = (int) DB::table('transport_vehicle_type')
                ->where('id', $vehicleTypeId)
                ->value('service_id');
            if ($serviceId > 0) {
                return $serviceId;
            }
        }

        $sharedServiceId = (int) DB::table('vehicle_services')
            ->where('service_mode', self::MODE)
            ->value('id');

        return $sharedServiceId > 0 ? $sharedServiceId : 1;
    }

    private static function createChatRideForSharedTrip(object $offer, User $passenger, User $driver): int
    {
        $now = Carbon::now(config('app.timezone'));
        $tripDate = Carbon::parse($offer->trip_date, config('app.timezone'));

        $ride = new TransportRideBook();
        $ride->user_id = $passenger->id;
        $ride->driver_id = $driver->id;
        $ride->user_name = trim($passenger->first_name . ' ' . ($passenger->last_name ?? ''));
        $ride->pickup_address = $offer->origin_town;
        $ride->destination_address = $offer->destination_town;
        $ride->pickup_lat = $passenger->current_lat ?? 0;
        $ride->pickup_long = $passenger->current_long ?? 0;
        $ride->destination_latlong = ($passenger->current_lat ?? 0) . ',' . ($passenger->current_long ?? 0);
        $ride->vehicle_service_id = self::resolveVehicleServiceId($offer);
        $ride->status = 0;
        $ride->ride_type = 1;
        $ride->payment_type = 1;
        $ride->payment_status = 0;
        $ride->is_auto_accept = 1;
        $ride->offered_price = (float) ($offer->fare_per_person ?? 0);
        $ride->total_pay = (float) ($offer->fare_per_person ?? 0);
        $ride->eta = 0;
        $ride->total_distance = 0;
        $ride->pickup_datetime = $tripDate->format('Y-m-d') . ' 08:00:00';
        $ride->retry_time = $now->format('Y-m-d H:i:s');
        $ride->ride_time_out = $tripDate->copy()->endOfDay()->format('Y-m-d H:i:s');
        $ride->save();

        return (int) $ride->id;
    }
}
