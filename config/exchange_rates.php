<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Live exchange rate sync (base: COP)
    |--------------------------------------------------------------------------
    | Free provider: https://www.exchangerate-api.com/docs/free (open access)
    | Rates are "target currency units per 1 COP", matching world_currency.ratio.
    */
    'base_currency' => env('EXCHANGE_RATE_BASE', 'COP'),
    'api_url' => env('EXCHANGE_RATE_API_URL', 'https://open.er-api.com/v6/latest/COP'),
    'timeout_seconds' => (int) env('EXCHANGE_RATE_TIMEOUT', 20),
    'min_ratio' => 0.0000001,
    'max_ratio' => 1000000,
];
