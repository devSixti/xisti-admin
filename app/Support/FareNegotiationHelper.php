<?php

namespace App\Support;

use App\Models\GeneralSettings;

final class FareNegotiationHelper
{
    public static function step(?GeneralSettings $general = null): int
    {
        $general = $general ?? request()->get('general_settings');

        return max(1, (int) ($general->fare_negotiation_step ?? 500));
    }

    /**
     * Snap a COP fare to the configured negotiation step (nearest multiple).
     */
    public static function snap(float $amount, ?int $step = null): float
    {
        $step = $step ?? self::step();
        if ($step <= 1) {
            return round($amount, 2);
        }

        return (float) (round($amount / $step) * $step);
    }

    public static function isValidStep(float $amount, ?int $step = null): bool
    {
        $step = $step ?? self::step();

        return fmod($amount, $step) <= 0.009 || fmod($amount, $step) >= ($step - 0.009);
    }
}
