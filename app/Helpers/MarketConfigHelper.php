<?php

namespace App\Helpers;

use App\Models\GeneralSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * @return array<string, mixed>
     */
    public static function isoDefaults(?string $iso): array
    {
        $map = self::catalog()['iso_defaults'] ?? [];
        $iso = strtoupper(trim((string) $iso));
        if ($iso !== '' && isset($map[$iso])) {
            return $map[$iso];
        }

        return $map['DEFAULT'] ?? [
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'dial_code' => '+1',
            'language' => 'en',
            'min_fare' => 5,
            'fare_step' => 1,
        ];
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
    public static function countryByIso(?string $iso): ?array
    {
        $iso = strtoupper(trim((string) $iso));
        if ($iso === '') {
            return null;
        }
        foreach (self::countries() as $country) {
            if (strtoupper((string) ($country['iso_code'] ?? '')) === $iso) {
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
                $resolved = self::resolvedPayload($country, $city, $lat, $lng, false);
            }
        } elseif ($lat !== null && $lng !== null) {
            $resolved = self::resolveFromCoordinates($lat, $lng);
        }

        return [
            'config_version' => (string) ($catalog['version'] ?? '1'),
            'default_country_id' => (string) ($catalog['default_country_id'] ?? 'co'),
            'default_city_id' => (string) ($catalog['default_city_id'] ?? 'medellin'),
            'iso_defaults' => $catalog['iso_defaults'] ?? [],
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
    private static function resolvedPayload(array $country, array $city, ?float $lat, ?float $lng, bool $geocodeDerived = false): array
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
            'is_geocode_derived' => $geocodeDerived,
        ];
    }

    /**
     * Catalog bbox first; outside catalog → reverse geocode (no snap to nearest market).
     *
     * @return array<string, mixed>|null
     */
    public static function resolveFromCoordinates(float $lat, float $lng): ?array
    {
        $country = self::countryContaining($lat, $lng);
        if ($country !== null) {
            $city = self::resolveCityInCountry($country, $lat, $lng, null);

            return self::resolvedPayload($country, $city, $lat, $lng, false);
        }

        $geo = self::reverseGeocode($lat, $lng);
        if ($geo === null) {
            return null;
        }

        return self::resolvedFromGeocode($geo, $lat, $lng);
    }

    /**
     * @param  array{iso:string,country_name:string,city_name:string}  $geo
     * @return array<string, mixed>
     */
    public static function resolvedFromGeocode(array $geo, float $lat, float $lng): array
    {
        $iso = strtoupper($geo['iso'] ?? '');
        $catalogCountry = self::countryByIso($iso);
        if ($catalogCountry !== null) {
            $city = self::resolveCityInCountry($catalogCountry, $lat, $lng, null);

            return self::resolvedPayload($catalogCountry, $city, $lat, $lng, true);
        }

        $defaults = self::isoDefaults($iso);
        $countryName = trim((string) ($geo['country_name'] ?? $iso));
        $cityName = trim((string) ($geo['city_name'] ?? $countryName));
        $countryId = 'geo_'.strtolower($iso !== '' ? $iso : 'xx');
        $citySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $cityName) ?: 'city');
        $cityId = $countryId.'_'.$citySlug;

        $country = [
            'id' => $countryId,
            'iso_code' => $iso !== '' ? $iso : 'XX',
            'display_name' => $countryName !== '' ? $countryName : ($iso !== '' ? $iso : 'Unknown'),
            'currency_code' => $defaults['currency_code'],
            'currency_symbol' => $defaults['currency_symbol'],
            'dial_code' => $defaults['dial_code'],
            'default_language_code' => $defaults['language'],
            'min_fare' => (float) $defaults['min_fare'],
            'fare_negotiation_step' => (float) $defaults['fare_step'],
            'bounds' => [
                'min_lat' => $lat - 5,
                'max_lat' => $lat + 5,
                'min_lng' => $lng - 5,
                'max_lng' => $lng + 5,
            ],
            'cities' => [],
        ];
        $city = [
            'id' => $cityId,
            'display_name' => $cityName !== '' ? $cityName : $country['display_name'],
            'center_lat' => $lat,
            'center_lng' => $lng,
            'min_lat' => $lat - 0.15,
            'max_lat' => $lat + 0.15,
            'min_lng' => $lng - 0.15,
            'max_lng' => $lng + 0.15,
            'zones' => [],
        ];
        $country['cities'] = [$city];

        return self::resolvedPayload($country, $city, $lat, $lng, true);
    }

    /**
     * @return array{iso:string,country_name:string,city_name:string}|null
     */
    public static function reverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $settings = GeneralSettings::query()->first();
            $serverKey = trim((string) ($settings->server_map_key ?? ''));
            if ($serverKey === '' || str_starts_with($serverKey, 'CHANGE_ME')) {
                return null;
            }

            $url = GoogleMapsProxyHelper::appendServerKey(
                'https://maps.googleapis.com/maps/api/geocode/json?latlng='.urlencode($lat.','.$lng).'&language=es',
                $serverKey
            );
            if (! GoogleMapsProxyHelper::isAllowedUrl($url)) {
                return null;
            }

            $response = Http::timeout(8)->get($url);
            if (! $response->ok()) {
                return null;
            }
            $json = $response->json();
            $results = $json['results'] ?? [];
            if (! is_array($results) || $results === []) {
                return null;
            }

            $iso = '';
            $countryName = '';
            $cityName = '';
            foreach ($results as $result) {
                foreach ($result['address_components'] ?? [] as $component) {
                    $types = $component['types'] ?? [];
                    if (in_array('country', $types, true)) {
                        $iso = (string) ($component['short_name'] ?? '');
                        $countryName = (string) ($component['long_name'] ?? '');
                    }
                    if ($cityName === '' && (in_array('locality', $types, true) || in_array('administrative_area_level_2', $types, true))) {
                        $cityName = (string) ($component['long_name'] ?? '');
                    }
                    if ($cityName === '' && in_array('administrative_area_level_1', $types, true)) {
                        $cityName = (string) ($component['long_name'] ?? '');
                    }
                }
                if ($iso !== '') {
                    break;
                }
            }

            if ($iso === '') {
                return null;
            }

            return [
                'iso' => $iso,
                'country_name' => $countryName,
                'city_name' => $cityName !== '' ? $cityName : $countryName,
            ];
        } catch (\Throwable $e) {
            Log::warning('MarketConfigHelper reverseGeocode failed: '.$e->getMessage());

            return null;
        }
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
            if ($cities !== []) {
                return self::nearestCity($cities, $lat, $lng);
            }
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
