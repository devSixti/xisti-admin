<?php

namespace App\Helpers;

use App\Models\ServiceSettings;

class RideInvoiceHelper
{
    /**
     * Desglose comisión plataforma + IVA sobre comisión (valores en moneda base del viaje).
     */
    public static function breakdown(
        float $tripValue,
        ?object $generalSettings = null,
        ?int $vehicleServiceId = null,
        ?string $deliveryVariant = null
    ): array {
        $general = $generalSettings ?? request()->get('general_settings');

        $commissionPercent = VehicleCommissionHelper::resolvePercent($vehicleServiceId, $deliveryVariant);

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

    public static function breakdownForRide(object $ride, ?object $generalSettings = null): array
    {
        $tripValue = (float) ($ride->offered_price ?? $ride->total_pay ?? 0);
        $vehicleServiceId = isset($ride->vehicle_service_id) ? (int) $ride->vehicle_service_id : null;
        $deliveryVariant = $ride->delivery_variant ?? null;

        return self::breakdown($tripValue, $generalSettings, $vehicleServiceId, $deliveryVariant);
    }

    public static function breakdownForCurrency(
        float $tripValueBase,
        float $currencyRatio,
        ?object $generalSettings = null,
        ?int $vehicleServiceId = null,
        ?string $deliveryVariant = null
    ): array {
        $base = self::breakdown($tripValueBase, $generalSettings, $vehicleServiceId, $deliveryVariant);
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
