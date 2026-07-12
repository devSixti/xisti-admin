<?php

namespace App\Helpers;

use App\Models\GeneralSettings;
use App\Models\ServiceSettings;
use App\Services\AppAuthorizationService;

class AppMobileSettingsHelper
{
    /**
     * Validation rules for package_weight_kg / package_*_cm on book-ride (service_id 4).
     * Controlled from admin → Site Settings → Mobile v1.0.2 (optional).
     *
     * @return array<string, string>
     */
    public static function courierPackageDimensionValidationRules(): array
    {
        $presence = MobileFeatureFlagsHelper::requireCourierPackageDimensions()
            ? 'required_if:service_id,==,4'
            : 'nullable';

        return [
            'package_weight_kg' => "{$presence}|numeric|min:0.1",
            'package_height_cm' => "{$presence}|numeric|min:1",
            'package_width_cm' => "{$presence}|numeric|min:1",
            'package_length_cm' => "{$presence}|numeric|min:1",
        ];
    }

    /**
     * @param  array<string, mixed>  $requestData
     * @return array<string, mixed>
     */
    public static function normalizeOptionalCourierPackageFields(array $requestData): array
    {
        foreach (['package_weight_kg', 'package_height_cm', 'package_width_cm', 'package_length_cm'] as $field) {
            if (!isset($requestData[$field]) || $requestData[$field] === '' || $requestData[$field] === null) {
                $requestData[$field] = null;
            }
        }

        return $requestData;
    }

    /**
     * @param  \Illuminate\Http\Request|\ArrayAccess  $request
     */
    public static function applyCourierPackageMetricsToModel($courierDetails, $request): void
    {
        foreach (['package_weight_kg', 'package_height_cm', 'package_width_cm', 'package_length_cm'] as $field) {
            $value = $request->get($field);
            if ($value !== null && $value !== '') {
                $courierDetails->{$field} = round((float) $value, 2);
            } else {
                $courierDetails->{$field} = null;
            }
        }
    }

    /**
     * Negotiation step for the passenger's currency.
     * Admin stores a COP-scale step (e.g. 500); foreign currencies must not receive that
     * value or mobile snap(recommended_usd / 500) collapses fares to 0.
     *
     * @param  \App\Models\WorldCurrency|null  $userCurrency
     */
    public static function negotiationStepForCurrency(?GeneralSettings $general = null, $userCurrency = null): int
    {
        if ($general === null) {
            try {
                $general = request()->get('general_settings');
            } catch (\Throwable $e) {
                $general = null;
            }
        }
        $base = max(1, (int) (optional($general)->fare_negotiation_step ?? 500));
        if ($userCurrency === null) {
            return $base;
        }

        $code = strtoupper((string) ($userCurrency->currency_code ?? ''));
        if ($code === '' || $code === 'COP') {
            return $base;
        }

        // High-denomination currencies can keep a large integer step.
        $largeUnit = ['ARS', 'CLP', 'PYG', 'CRC', 'UYU', 'JPY', 'VND', 'IDR'];
        if (in_array($code, $largeUnit, true)) {
            return $base;
        }

        $ratio = (float) ($userCurrency->ratio ?? 0);
        if ($ratio <= 0) {
            return 1;
        }

        return max(1, (int) round($base * $ratio));
    }

    /**
     * @param  \App\Models\WorldCurrency|null  $userCurrency
     */
    public static function pricingAndCommissionPayload(?GeneralSettings $general = null, $userCurrency = null): array
    {
        $general = $general ?? request()->get('general_settings');
        $service = ServiceSettings::query()->first();

        return array_merge([
            'fare_negotiation_step' => self::negotiationStepForCurrency($general, $userCurrency),
            'vat_rate_on_commission' => (float) ($general->vat_rate_on_commission ?? 19),
            'admin_commission_percent' => (float) ($service->admin_commission ?? config('xisti.default_commission_percent', 8)),
            'commission_rates_by_variant' => VehicleCommissionHelper::ratesMapForMobile(),
            'driver_can_cancel_until_status' => (int) ($general->driver_cancel_until_status ?? 3),
            'destination_payment_methods' => DestinationPaymentHelper::catalogForMobileApi($general),
        ], MobileFeatureFlagsHelper::apiPayload($general));
    }

    public static function driverCanCancelRide(int $rideStatus, ?GeneralSettings $general = null): int
    {
        $general = $general ?? request()->get('general_settings');
        $until = (int) ($general->driver_cancel_until_status ?? 3);

        return $rideStatus <= $until ? 1 : 0;
    }

    /**
     * Mobile bootstrap: app_key for Authorization header (public endpoints only).
     *
     * @return array{app_key?: string}
     */
    public static function mobileBootstrapAppKeyPayload(): array
    {
        $appKey = app(AppAuthorizationService::class)->resolveAppKey();
        if ($appKey === '') {
            return [];
        }

        return ['app_key' => $appKey];
    }
}
