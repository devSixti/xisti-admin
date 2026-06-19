<?php

/**
 * XISTI market catalog — single source of truth for mobile regional config.
 * Countries: CO, US, BR, AR. Override per-env via XISTI_MARKETS_VERSION.
 */
return [
    'version' => env('XISTI_MARKETS_VERSION', '2026-06-03'),
    'default_country_id' => 'co',
    'default_city_id' => 'medellin',
    'countries' => [
        [
            'id' => 'co',
            'iso_code' => 'CO',
            'display_name' => 'Colombia',
            'currency_code' => 'COP',
            'currency_symbol' => 'COL$',
            'dial_code' => '+57',
            'default_language_code' => 'es',
            'min_fare' => 5000,
            'fare_negotiation_step' => 500,
            'bounds' => ['min_lat' => -4.5, 'max_lat' => 13.5, 'min_lng' => -79.5, 'max_lng' => -66.5],
            'cities' => [
                ['id' => 'bogota', 'display_name' => 'Bogotá', 'center_lat' => 4.7110, 'center_lng' => -74.0721, 'min_lat' => 4.45, 'max_lat' => 4.85, 'min_lng' => -74.25, 'max_lng' => -73.95],
                ['id' => 'medellin', 'display_name' => 'Medellín', 'center_lat' => 6.2476, 'center_lng' => -75.5658, 'min_lat' => 6.05, 'max_lat' => 6.50, 'min_lng' => -75.75, 'max_lng' => -75.30],
                ['id' => 'barranquilla', 'display_name' => 'Barranquilla', 'center_lat' => 10.9639, 'center_lng' => -74.7964, 'min_lat' => 10.82, 'max_lat' => 11.05, 'min_lng' => -74.98, 'max_lng' => -74.65],
                ['id' => 'cali', 'display_name' => 'Cali', 'center_lat' => 3.4516, 'center_lng' => -76.5320, 'min_lat' => 3.25, 'max_lat' => 3.55, 'min_lng' => -76.65, 'max_lng' => -76.42],
                ['id' => 'bucaramanga', 'display_name' => 'Bucaramanga', 'center_lat' => 7.1193, 'center_lng' => -73.1227, 'min_lat' => 6.98, 'max_lat' => 7.22, 'min_lng' => -73.28, 'max_lng' => -73.05],
                ['id' => 'cartagena', 'display_name' => 'Cartagena', 'center_lat' => 10.3910, 'center_lng' => -75.4794, 'min_lat' => 10.28, 'max_lat' => 10.48, 'min_lng' => -75.58, 'max_lng' => -75.44],
                ['id' => 'manizales', 'display_name' => 'Manizales', 'center_lat' => 5.0703, 'center_lng' => -75.5138, 'min_lat' => 4.97, 'max_lat' => 5.17, 'min_lng' => -75.58, 'max_lng' => -75.42],
            ],
        ],
        [
            'id' => 'us',
            'iso_code' => 'US',
            'display_name' => 'Estados Unidos',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'dial_code' => '+1',
            'default_language_code' => 'en',
            'min_fare' => 8,
            'fare_negotiation_step' => 1,
            'bounds' => ['min_lat' => 24.5, 'max_lat' => 49.5, 'min_lng' => -125.0, 'max_lng' => -66.5],
            'cities' => [
                ['id' => 'miami', 'display_name' => 'Miami', 'center_lat' => 25.7617, 'center_lng' => -80.1918, 'min_lat' => 25.55, 'max_lat' => 26.00, 'min_lng' => -80.45, 'max_lng' => -80.05],
                ['id' => 'new_york', 'display_name' => 'New York', 'center_lat' => 40.7128, 'center_lng' => -74.0060, 'min_lat' => 40.45, 'max_lat' => 40.95, 'min_lng' => -74.30, 'max_lng' => -73.70],
                ['id' => 'los_angeles', 'display_name' => 'Los Angeles', 'center_lat' => 34.0522, 'center_lng' => -118.2437, 'min_lat' => 33.70, 'max_lat' => 34.35, 'min_lng' => -118.65, 'max_lng' => -117.90],
                ['id' => 'chicago', 'display_name' => 'Chicago', 'center_lat' => 41.8781, 'center_lng' => -87.6298, 'min_lat' => 41.65, 'max_lat' => 42.10, 'min_lng' => -87.95, 'max_lng' => -87.50],
                ['id' => 'houston', 'display_name' => 'Houston', 'center_lat' => 29.7604, 'center_lng' => -95.3698, 'min_lat' => 29.50, 'max_lat' => 30.10, 'min_lng' => -95.80, 'max_lng' => -95.00],
            ],
        ],
        [
            'id' => 'br',
            'iso_code' => 'BR',
            'display_name' => 'Brasil',
            'currency_code' => 'BRL',
            'currency_symbol' => 'R$',
            'dial_code' => '+55',
            'default_language_code' => 'pt',
            'min_fare' => 12,
            'fare_negotiation_step' => 2,
            'bounds' => ['min_lat' => -34.0, 'max_lat' => 5.5, 'min_lng' => -74.0, 'max_lng' => -34.0],
            'cities' => [
                ['id' => 'sao_paulo', 'display_name' => 'São Paulo', 'center_lat' => -23.5505, 'center_lng' => -46.6333, 'min_lat' => -23.75, 'max_lat' => -23.35, 'min_lng' => -46.85, 'max_lng' => -46.35],
                ['id' => 'rio_de_janeiro', 'display_name' => 'Rio de Janeiro', 'center_lat' => -22.9068, 'center_lng' => -43.1729, 'min_lat' => -23.10, 'max_lat' => -22.75, 'min_lng' => -43.70, 'max_lng' => -43.10],
                ['id' => 'brasilia', 'display_name' => 'Brasília', 'center_lat' => -15.7939, 'center_lng' => -47.8828, 'min_lat' => -15.95, 'max_lat' => -15.65, 'min_lng' => -48.10, 'max_lng' => -47.65],
            ],
        ],
        [
            'id' => 'ar',
            'iso_code' => 'AR',
            'display_name' => 'Argentina',
            'currency_code' => 'ARS',
            'currency_symbol' => 'AR$',
            'dial_code' => '+54',
            'default_language_code' => 'es',
            'min_fare' => 2500,
            'fare_negotiation_step' => 500,
            'bounds' => ['min_lat' => -55.0, 'max_lat' => -21.5, 'min_lng' => -73.5, 'max_lng' => -53.0],
            'cities' => [
                ['id' => 'buenos_aires', 'display_name' => 'Buenos Aires', 'center_lat' => -34.6037, 'center_lng' => -58.3816, 'min_lat' => -34.75, 'max_lat' => -34.45, 'min_lng' => -58.55, 'max_lng' => -58.30],
                ['id' => 'cordoba', 'display_name' => 'Córdoba', 'center_lat' => -31.4201, 'center_lng' => -64.1888, 'min_lat' => -31.55, 'max_lat' => -31.30, 'min_lng' => -64.35, 'max_lng' => -64.05],
                ['id' => 'rosario', 'display_name' => 'Rosario', 'center_lat' => -32.9468, 'center_lng' => -60.6393, 'min_lat' => -33.10, 'max_lat' => -32.80, 'min_lng' => -60.80, 'max_lng' => -60.50],
            ],
        ],
    ],
];
