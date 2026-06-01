<?php

namespace App\Helpers;

use App\Models\ServiceSettings;

class RideInvoiceHelper
{
    /**
     * Desglose comisión plataforma + IVA sobre comisión (valores en moneda base del viaje).
     */
    public static function breakdown(float $tripValue, ?object $generalSettings = null): array
    {
        $general = $generalSettings ?? request()->get('general_settings');
        $service = ServiceSettings::query()->first();

        $commissionPercent = (float) config('xisti.default_commission_percent', 8);
        if ($service && $service->admin_commission > 0) {
            $commissionPercent = (float) $service->admin_commission;
        }

        $vatRate = (float) ($general->vat_rate_on_commission ?? 19);

        $commission = round($tripValue * ($commissionPercent / 100), 2);
        $vat = round($commission * ($vatRate / 100), 2);
        $totalDeduction = round($commission + $vat, 2);
        $netDriver = round(max($tripValue - $totalDeduction, 0), 2);

        return [
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commission,
            'vat_rate_on_commission' => $vatRate,
            'vat_on_commission' => $vat,
            'total_deduction' => $totalDeduction,
            'net_driver_pay' => $netDriver,
            'trip_value' => round($tripValue, 2),
        ];
    }

    public static function breakdownForCurrency(float $tripValueBase, float $currencyRatio, ?object $generalSettings = null): array
    {
        $base = self::breakdown($tripValueBase, $generalSettings);
        $ratio = $currencyRatio > 0 ? $currencyRatio : 1;

        return [
            'commission_percent' => $base['commission_percent'],
            'vat_rate_on_commission' => $base['vat_rate_on_commission'],
            'commission_amount' => round($base['commission_amount'] * $ratio, 2),
            'vat_on_commission' => round($base['vat_on_commission'] * $ratio, 2),
            'total_deduction' => round($base['total_deduction'] * $ratio, 2),
            'net_driver_pay' => round($base['net_driver_pay'] * $ratio, 2),
            'trip_value' => round($base['trip_value'] * $ratio, 2),
        ];
    }
}
