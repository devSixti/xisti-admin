<?php

namespace App\Helpers;

class GoogleMapsProxyHelper
{
    private const ALLOWED_HOSTS = [
        'maps.googleapis.com',
        'maps.google.com',
    ];

    private const ALLOWED_PATH_PREFIXES = [
        '/maps/api/geocode/',
        '/maps/api/directions/',
        '/maps/api/place/autocomplete/',
        '/maps/api/place/details/',
        '/maps/api/distancematrix/',
        '/maps/api/elevation/',
    ];

    public static function isAllowedUrl(string $url): bool
    {
        $parts = parse_url(trim($url));
        if ($parts === false || empty($parts['host']) || empty($parts['path'])) {
            return false;
        }

        $host = strtolower((string) $parts['host']);
        if (! in_array($host, self::ALLOWED_HOSTS, true)) {
            return false;
        }

        $path = strtolower((string) $parts['path']);
        foreach (self::ALLOWED_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public static function appendServerKey(string $url, string $serverKey): string
    {
        $url = rtrim($url);
        if (str_ends_with($url, '&key=') || str_ends_with($url, '?key=')) {
            return $url.$serverKey;
        }
        $sep = str_contains($url, '?') ? '&' : '?';

        return $url.$sep.'key='.urlencode($serverKey);
    }
}
