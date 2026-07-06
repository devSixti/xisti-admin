<?php

return [
    'product_name' => env('XISTI_PRODUCT_NAME', 'XISTI'),
    'tagline' => env('XISTI_TAGLINE', 'Fácil y Seguro'),
    'app_key' => env('XISTI_APP_KEY'),
    /** @deprecated QA OTP is limited to seeded test users via QaTestUserHelper. */
    'otp_bypass' => false,
    'allowed_admin_host' => env('XISTI_ALLOWED_ADMIN_HOST', 'admin.xistiapp.com'),
    'public_site_url' => env('XISTI_PUBLIC_SITE_URL', 'https://www.xistiapp.com'),
    'default_commission_percent' => (float) env('XISTI_DEFAULT_COMMISSION_PERCENT', 8),
    'default_city' => env('XISTI_DEFAULT_CITY', 'Medellín'),
    'default_country' => env('XISTI_DEFAULT_COUNTRY', 'Colombia'),
    'fare_negotiation_step_cop' => (int) env('XISTI_FARE_NEGOTIATION_STEP', 500),
    /** Max Google Maps proxy calls per device/session per day (autocomplete, geocode, routes). */
    'maps_daily_limit' => (int) env('XISTI_MAPS_DAILY_LIMIT', 10000),
    'firebase_database_url' => env('FIREBASE_DATABASE_URL'),
    'fcm_user_topic' => env('FIREBASE_FCM_USER_TOPIC', 'XistiUser'),
    'fcm_driver_topic' => env('FIREBASE_FCM_DRIVER_TOPIC', 'XistiDriver'),
    'firebase_chat_admin_email' => env('XISTI_FIREBASE_CHAT_ADMIN_EMAIL', 'admin@xistiapp.com'),
    'firebase_chat_admin_password' => env('XISTI_FIREBASE_CHAT_ADMIN_PASSWORD'),
    'brand' => [
        'primary' => '#80FF00',
        'secondary' => '#681FFF',
        'background' => '#0B0B0B',
        'surface' => '#141414',
    ],
    'mail' => [
        /** Outbound transactional sender (Resend / SMTP From). */
        'from_address' => env('XISTI_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'noreply@xistiapp.com')),
        'from_name' => env('XISTI_MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'XISTI')),
        /** Inbound support / admin notifications (Reply-To / site contact). */
        'support_address' => env('XISTI_SUPPORT_EMAIL', 'soporte@xistiapp.com'),
        /** Public URL for hosted email assets (logo, header pattern). */
        'logo_url' => env('XISTI_MAIL_LOGO_URL', ''),
    ],
];
