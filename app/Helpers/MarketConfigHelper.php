<?php

namespace App\Helpers;

class MarketConfigHelper
{
    /**
     * @return array<string, mixed>
     */
    public static function catalog(): array
    {
        return config('markets', []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function countries(): array
    {
        return self::catalog()['countries'] ?? [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function countryById(?string $countryId): ?array
    {
        if ($countryId === null || trim($countryId) === '') {
            return null;
        }
        foreach (self::countries() as $country) {
            if (($country['id'] ?? '') === $countryId) {
                return $country;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function cityById(?string $cityId): ?array
    {
        if ($cityId === null || trim($cityId) === '') {
            return null;
        }
        foreach (self::countries() as $country) {
            foreach ($country['cities'] ?? [] as $city) {
                if (($city['id'] ?? '') === $cityId) {
                    return array_merge($city, [
                        'country_id' => $country['id'],
                        'country' => self::countryPayload($country),
                    ]);
                }
            }
        }

        return null;
    }

  /**
     * @return array<string, mixed>
     */
    public static function apiPayload(?float $lat = null, ?float $lng = null, ?string $countryId = null, ?string $cityId = null): array
    {
        $catalog = self::catalog();
        $resolved = null;

        if ($countryId !== null && trim($countryId) !== '') {
            $country = self::countryById($countryId);
            if ($country !== null) {
                $city = self::resolveCityInCountry($country, $lat, $lng, $cityId);
                $resolved = self::resolvedPayload($country, $city, $lat, $lng);
            }
        } elseif ($lat !== null && $lng !== null) {
            $resolved = self::resolveFromCoordinates($lat, $lng);
        }

        return [
            'config_version' => (string) ($catalog['version'] ?? '1'),
            'default_country_id' => (string) ($catalog['default_country_id'] ?? 'co'),
            'default_city_id' => (string) ($catalog['default_city_id'] ?? 'medellin'),
            'countries' => array_map(static fn (array $c) => self::countryPayload($c), self::countries()),
            'resolved' => $resolved,
        ];
    }

    /**
     * @param  array<string, mixed>  $country
     * @return array<string, mixed>
     */
    private static function countryPayload(array $country): array
    {
        return [
            'id' => $country['id'],
            'iso_code' => $country['iso_code'] ?? '',
            'display_name' => $country['display_name'] ?? '',
            'currency_code' => $country['currency_code'] ?? '',
            'currency_symbol' => $country['currency_symbol'] ?? '',
            'dial_code' => $country['dial_code'] ?? '',
            'default_language_code' => $country['default_language_code'] ?? 'es',
            'min_fare' => (float) ($country['min_fare'] ?? 0),
            'fare_negotiation_step' => (float) ($country['fare_negotiation_step'] ?? 1),
            'bounds' => $country['bounds'] ?? [],
            'cities' => array_map(static fn (array $city) => self::cityPayload($city, (string) $country['id']), $country['cities'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $city
     * @return array<string, mixed>
     */
    private static function cityPayload(array $city, string $countryId): array
    {
        return [
            'id' => $city['id'],
            'country_id' => $countryId,
            'display_name' => $city['display_name'] ?? '',
            'center_lat' => (float) ($city['center_lat'] ?? 0),
            'center_lng' => (float) ($city['center_lng'] ?? 0),
            'min_lat' => (float) ($city['min_lat'] ?? 0),
            'max_lat' => (float) ($city['max_lat'] ?? 0),
            'min_lng' => (float) ($city['min_lng'] ?? 0),
            'max_lng' => (float) ($city['max_lng'] ?? 0),
            'zones' => $city['zones'] ?? [],
        ];
    }

    /**
     * @param  array<string, mixed>  $country
     * @param  array<string, mixed>  $city
     * @return array<string, mixed>
     */
    private static function resolvedPayload(array $country, array $city, ?float $lat, ?float $lng): array
    {
        $inside = false;
        if ($lat !== null && $lng !== null) {
            $inside = self::contains($city, $lat, $lng);
        }

        return [
            'country_id' => $country['id'],
            'city_id' => $city['id'],
            'country' => self::countryPayload($country),
            'city' => self::cityPayload($city, (string) $country['id']),
            'is_inside_city_bounds' => $inside,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function resolveFromCoordinates(float $lat, float $lng): array
    {
        $country = self::countryContaining($lat, $lng) ?? self::nearestCountry($lat, $lng);
        $city = self::resolveCityInCountry($country, $lat, $lng, null);

        return self::resolvedPayload($country, $city, $lat, $lng);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function countryContaining(float $lat, float $lng): ?array
    {
        foreach (self::countries() as $country) {
            if (self::contains($country['bounds'] ?? [], $lat, $lng)) {
                return $country;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function nearestCountry(float $lat, float $lng): array
    {
        $countries = self::countries();
        $nearest = $countries[0];
        $nearestDistance = PHP_FLOAT_MAX;

        foreach ($countries as $country) {
            $bounds = $country['bounds'] ?? [];
            $centerLat = (($bounds['min_lat'] ?? 0) + ($bounds['max_lat'] ?? 0)) / 2;
            $centerLng = (($bounds['min_lng'] ?? 0) + ($bounds['max_lng'] ?? 0)) / 2;
            $distance = self::haversineKm($lat, $lng, $centerLat, $centerLng);
            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearest = $country;
            }
        }

        return $nearest;
    }

    /**
     * @param  array<string, mixed>  $country
     * @return array<string, mixed>
     */
    private static function resolveCityInCountry(array $country, ?float $lat, ?float $lng, ?string $cityId): array
    {
        $cities = $country['cities'] ?? [];
        if ($cityId !== null && trim($cityId) !== '') {
            foreach ($cities as $city) {
                if (($city['id'] ?? '') === $cityId) {
                    return $city;
                }
            }
        }
        if ($lat !== null && $lng !== null) {
            foreach ($cities as $city) {
                if (self::contains($city, $lat, $lng)) {
                    return $city;
                }
            }
            return self::nearestCity($cities, $lat, $lng);
        }

        return $cities[0] ?? ['id' => '', 'display_name' => ''];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cities
     * @return array<string, mixed>
     */
    private static function nearestCity(array $cities, float $lat, float $lng): array
    {
        $nearest = $cities[0];
        $nearestDistance = PHP_FLOAT_MAX;
        foreach ($cities as $city) {
            $distance = self::haversineKm($lat, $lng, (float) ($city['center_lat'] ?? 0), (float) ($city['center_lng'] ?? 0));
            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearest = $city;
            }
        }

        return $nearest;
    }

    /**
     * @param  array<string, float>  $box
     */
    private static function contains(array $box, float $lat, float $lng): bool
    {
        if (! isset($box['min_lat'], $box['max_lat'], $box['min_lng'], $box['max_lng'])) {
            return false;
        }

        return $lat >= $box['min_lat'] && $lat <= $box['max_lat']
            && $lng >= $box['min_lng'] && $lng <= $box['max_lng'];
    }

    private static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
