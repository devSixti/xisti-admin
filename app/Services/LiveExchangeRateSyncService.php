<?php

namespace App\Services;

use App\Models\WorldCurrency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiveExchangeRateSyncService
{
    /**
     * @return array{updated: int, skipped: int, base: string, provider_time: ?string}
     */
    public function sync(bool $dryRun = false): array
    {
        $base = strtoupper((string) config('exchange_rates.base_currency', 'COP'));
        $url = trim((string) config('exchange_rates.api_url'));
        if ($url === '') {
            $url = 'https://open.er-api.com/v6/latest/'.urlencode($base);
        }
        $timeout = (int) config('exchange_rates.timeout_seconds', 20);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException('Exchange rate API HTTP '.$response->status());
        }

        $payload = $response->json();
        if (! is_array($payload) || ($payload['result'] ?? '') !== 'success') {
            throw new \RuntimeException('Exchange rate API returned invalid payload');
        }

        $apiBase = strtoupper((string) ($payload['base_code'] ?? ''));
        if ($apiBase !== $base) {
            throw new \RuntimeException("Exchange rate API base {$apiBase} != configured {$base}");
        }

        $rates = $payload['rates'] ?? [];
        if (! is_array($rates) || $rates === []) {
            throw new \RuntimeException('Exchange rate API returned empty rates');
        }

        $minRatio = (float) config('exchange_rates.min_ratio', 0.0000001);
        $maxRatio = (float) config('exchange_rates.max_ratio', 1000000);
        $updated = 0;
        $skipped = 0;

        $currencies = WorldCurrency::query()
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        foreach ($currencies as $currency) {
            $code = strtoupper((string) $currency->currency_code);

            if ($code === $base) {
                if (! $dryRun && (float) $currency->ratio !== 1.0) {
                    $currency->ratio = 1.0;
                    $currency->save();
                    $updated++;
                }
                continue;
            }

            if (! array_key_exists($code, $rates)) {
                $skipped++;
                Log::warning('LiveExchangeRateSync: missing rate for '.$code);

                continue;
            }

            $ratio = round((float) $rates[$code], 6);
            if ($ratio < $minRatio || $ratio > $maxRatio) {
                $skipped++;
                Log::warning('LiveExchangeRateSync: out-of-range ratio for '.$code, ['ratio' => $ratio]);

                continue;
            }

            if ($dryRun) {
                $updated++;

                continue;
            }

            if ((float) $currency->ratio === $ratio) {
                $skipped++;

                continue;
            }

            $currency->ratio = $ratio;
            $currency->save();
            $updated++;
        }

        $summary = [
            'updated' => $updated,
            'skipped' => $skipped,
            'base' => $base,
            'provider_time' => isset($payload['time_last_update_utc'])
                ? (string) $payload['time_last_update_utc']
                : null,
        ];

        Log::info('LiveExchangeRateSync completed', $summary);

        return $summary;
    }
}
