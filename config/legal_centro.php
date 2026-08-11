<?php

return [
    'brand_name' => env('XISTI_PRODUCT_NAME', 'XISTI'),
    'tagline' => env('XISTI_TAGLINE', 'Fácil y Seguro'),
    'consent_version' => env('XISTI_LEGAL_CONSENT_VERSION', '2026-08-legal-v2'),
    'last_updated' => env('XISTI_LEGAL_LAST_UPDATED', '2026-08-11'),
    'primary_color' => '#80FF00',
    'primary_hover' => '#6de600',
    'logo_path' => env('XISTI_LEGAL_LOGO', 'assets/images/website-logo-icon/xisti-logo.svg'),
    'entity' => [
        'name' => env('XISTI_LEGAL_ENTITY_NAME', 'XISTI Tecnología S.A.S.'),
        'nit' => env('XISTI_LEGAL_ENTITY_NIT', ''),
        'address' => env('XISTI_LEGAL_ENTITY_ADDRESS', 'Colombia'),
        'city' => env('XISTI_LEGAL_ENTITY_CITY', ''),
        'country' => env('XISTI_LEGAL_ENTITY_COUNTRY', 'Colombia'),
    ],
];
