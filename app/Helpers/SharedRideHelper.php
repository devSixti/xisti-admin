<?php

namespace App\Helpers;

use App\Helpers\FcmPushHelper;
use App\Models\TransportRideBook;
use App\Models\User;
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

    public static function passengerDisclaimer(string $language = 'es'): string
    {
        if ($language === 'es') {
            return 'Consiste en viaje compartido interurbano.';
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

            $title = 'Nuevo pasajero en tu viaje compartido';
            $body = trim($passenger->first_name . ' ' . ($passenger->last_name ?? ''))
                . ' — ' . $offer->origin_town . ' → ' . $offer->destination_town;
            if (trim((string) ($driver->device_token ?? '')) !== '') {
                FcmPushHelper::sendToToken((string) $driver->device_token, $title, $body, [
                    'notification_type' => '1',
                    'ride_id' => (string) $rideId,
                    'user_type' => '2',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);
            }

            return [
                'status' => 1,
                'message' => 'Te uniste al viaje compartido. Puedes chatear con el socio conductor.',
                'ride_id' => $rideId,
                'offer_id' => $offerId,
            ];
        });
    }

    private static function createChatRideForSharedTrip(object $offer, User $passenger, User $driver): int
    {
        $ride = new TransportRideBook();
        $ride->user_id = $passenger->id;
        $ride->driver_id = $driver->id;
        $ride->user_name = trim($passenger->first_name . ' ' . ($passenger->last_name ?? ''));
        $ride->pickup_location = $offer->origin_town;
        $ride->drop_location = $offer->destination_town;
        $ride->pickup_lat = $passenger->current_lat ?? 0;
        $ride->pickup_long = $passenger->current_long ?? 0;
        $ride->drop_lat = $passenger->current_lat ?? 0;
        $ride->drop_long = $passenger->current_long ?? 0;
        $ride->vehicle_service_id = 1;
        $ride->status = 2;
        $ride->ride_type = 0;
        $ride->payment_type = 1;
        $ride->offered_fare = (float) ($offer->fare_per_person ?? 0);
        $ride->estimated_time = 0;
        $ride->total_distance = 0;
        $ride->pickup_date_time = $offer->trip_date . ' 08:00:00';
        $ride->save();

        return (int) $ride->id;
    }
}
