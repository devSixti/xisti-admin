<?php

namespace Tests\Unit;

use App\Models\WorldCurrency;
use App\Services\LiveExchangeRateSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveExchangeRateSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_updates_ratios_from_cop_base_api(): void
    {
        config([
            'exchange_rates.api_url' => 'https://open.er-api.com/v6/latest/COP',
            'exchange_rates.base_currency' => 'COP',
        ]);

        WorldCurrency::query()->updateOrCreate(
            ['currency_code' => 'COP'],
            ['id' => 1, 'currency_name' => 'Colombian Peso', 'ratio' => 1, 'symbol' => 'COL$', 'status' => 1, 'default_currency' => 1]
        );
        WorldCurrency::query()->updateOrCreate(
            ['currency_code' => 'USD'],
            ['id' => 2, 'currency_name' => 'US Dollar', 'ratio' => 0.1, 'symbol' => '$', 'status' => 1, 'default_currency' => 0]
        );

        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'base_code' => 'COP',
                'time_last_update_utc' => 'Mon, 22 Jul 2026 00:00:01 +0000',
                'rates' => [
                    'USD' => 0.000311,
                    'EUR' => 0.000273,
                ],
            ], 200),
        ]);

        $result = (new LiveExchangeRateSyncService())->sync();

        $this->assertSame(2, $result['updated']);
        $this->assertSame(0.000311, (float) WorldCurrency::query()->where('currency_code', 'USD')->value('ratio'));
        $this->assertSame(1.0, (float) WorldCurrency::query()->where('currency_code', 'COP')->value('ratio'));
    }

    public function test_sync_keeps_existing_ratio_when_api_missing_currency(): void
    {
        config([
            'exchange_rates.api_url' => 'https://open.er-api.com/v6/latest/COP',
            'exchange_rates.base_currency' => 'COP',
        ]);

        WorldCurrency::query()->updateOrCreate(
            ['currency_code' => 'COP'],
            ['id' => 1, 'currency_name' => 'Colombian Peso', 'ratio' => 1, 'symbol' => 'COL$', 'status' => 1, 'default_currency' => 1]
        );
        WorldCurrency::query()->updateOrCreate(
            ['currency_code' => 'USD'],
            ['id' => 2, 'currency_name' => 'US Dollar', 'ratio' => 0.000278, 'symbol' => '$', 'status' => 1, 'default_currency' => 0]
        );

        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'base_code' => 'COP',
                'rates' => ['EUR' => 0.000273],
            ], 200),
        ]);

        $result = (new LiveExchangeRateSyncService())->sync();

        $this->assertSame(1, $result['updated']);
        $this->assertSame(1, $result['skipped']);
        $this->assertSame(0.000278, (float) WorldCurrency::query()->where('currency_code', 'USD')->value('ratio'));
    }
}
