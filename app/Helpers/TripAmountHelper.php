<?php

namespace App\Helpers;

class TripAmountHelper
{
    /**
     * Parse amounts from mobile/API (handles Colombian "15.000" without becoming 15.0 in PHP).
     */
    public static function parseDisplayAmount(mixed $raw, ?string $currencySymbolOrCode = null): float
    {
        if ($raw === null || $raw === '') {
            return 0.0;
        }
        if (is_int($raw) || is_float($raw)) {
            return round((float) $raw, 2);
        }

        $value = trim((string) $raw);
        if ($value === '' || strcasecmp($value, 'null') === 0) {
            return 0.0;
        }

        $currency = strtoupper(trim((string) ($currencySymbolOrCode ?? '')));
        $largeUnit = $currency === ''
            || str_contains($currency, 'COL')
            || str_contains($currency, 'COP')
            || in_array($currency, ['ARS', 'CLP', 'PYG', 'CRC', 'UYU', 'JPY', 'VND', 'IDR'], true);

        if ($largeUnit) {
            if (str_contains($value, ',')) {
                $parts = explode(',', $value, 2);
                $whole = str_replace('.', '', $parts[0]);
                $fraction = $parts[1] ?? '';

                return round((float) ($fraction === '' ? $whole : $whole.'.'.$fraction), 2);
            }
            if (preg_match('/^-?\d{1,3}(\.\d{3})+(\.\d+)?$/', $value)) {
                if (preg_match('/\.\d{1,2}$/', $value) && !preg_match('/\.\d{3}$/', $value)) {
                    return round((float) str_replace(',', '.', $value), 2);
                }

                return round((float) str_replace('.', '', $value), 2);
            }
        }

        $normalized = str_replace(',', '.', $value);

        return round((float) $normalized, 2);
    }

    /**
     * Trip amount in base (stored) currency: prefer total_pay, fall back to offered_price.
     */
    public static function resolveBase(object|array $ride): float
    {
        if (is_array($ride)) {
            $totalPay = (float) ($ride['total_pay'] ?? $ride['total_amount'] ?? 0);
            $offered = (float) ($ride['offered_price'] ?? 0);
        } else {
            $totalPay = (float) ($ride->total_pay ?? $ride->total_amount ?? 0);
            $offered = (float) ($ride->offered_price ?? 0);
        }

        if ($totalPay > 0) {
            return $totalPay;
        }

        return $offered > 0 ? $offered : 0;
    }

    public static function resolveForCurrency(object|array $ride, float $currencyRatio): float
    {
        $ratio = $currencyRatio > 0 ? $currencyRatio : 1;

        return round(self::resolveBase($ride) * $ratio, 2);
    }
}
