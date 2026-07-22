<?php

namespace App\Support;

use App\Models\WorldCurrency;

/**
 * Resolves a user's WorldCurrency row from symbol or ISO code.
 */
final class UserCurrencyResolver
{
    public static function forUser($user): ?WorldCurrency
    {
        $raw = trim((string) ($user->currency ?? ''));
        if ($raw === '') {
            return WorldCurrency::query()->where('default_currency', 1)->first();
        }

        $found = WorldCurrency::query()
            ->where(function ($q) use ($raw) {
                $q->where('symbol', $raw)->orWhere('currency_code', $raw);
            })
            ->first();

        return $found ?? WorldCurrency::query()->where('default_currency', 1)->first();
    }

    public static function ratioForUser($user): float
    {
        $currency = self::forUser($user);

        return $currency !== null ? (float) $currency->ratio : 1.0;
    }

    public static function forCurrency(?string $raw): ?WorldCurrency
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return WorldCurrency::query()->where('default_currency', 1)->first();
        }

        $found = WorldCurrency::query()
            ->where(function ($q) use ($raw) {
                $q->where('symbol', $raw)->orWhere('currency_code', $raw);
            })
            ->first();

        return $found ?? WorldCurrency::query()->where('default_currency', 1)->first();
    }

    public static function ratioForCurrency(?string $raw): float
    {
        $currency = self::forCurrency($raw);

        return $currency !== null ? (float) $currency->ratio : 1.0;
    }
}
