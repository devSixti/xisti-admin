<?php

namespace App\Helpers;

class TripAmountHelper
{
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
