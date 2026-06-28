<?php

namespace App\Support;

use App\Models\TransportVehicleType;
use App\Models\User;

class ColombiaFormValidation
{
    public static function isColombiaCountryCode(?string $countryCode): bool
    {
        $code = strtoupper(trim((string) $countryCode));
        if ($code === 'CO') {
            return true;
        }

        $digits = preg_replace('/\D/', '', $code);

        return $digits === '57';
    }

    public static function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D/', '', (string) $phone) ?? '';
    }

    /**
     * Canonical dial code for storage and uniqueness checks (+57 for Colombia).
     */
    public static function normalizeCountryDialCode(?string $countryCode): string
    {
        $raw = trim((string) $countryCode);
        if ($raw === '') {
            return '+57';
        }
        if (self::isColombiaCountryCode($raw)) {
            return '+57';
        }
        if (str_starts_with($raw, '+')) {
            return $raw;
        }
        $digits = self::normalizePhone($raw);

        return $digits !== '' ? '+'.$digits : '+57';
    }

    /**
     * Country code variants that may exist in legacy rows (+57, 57, CO).
     *
     * @return list<string>
     */
    public static function countryDialCodeVariants(?string $countryCode): array
    {
        $canonical = self::normalizeCountryDialCode($countryCode);
        $digits = self::normalizePhone($countryCode);
        $variants = array_filter([$canonical, $countryCode, $digits, '+'.$digits, 'CO']);
        if (self::isColombiaCountryCode($countryCode)) {
            $variants = array_merge($variants, ['+57', '57', 'CO']);
        }

        return array_values(array_unique(array_filter($variants, static fn ($v) => $v !== '' && $v !== null)));
    }

    public static function contactTakenByOtherUser(string $normalizedContact, ?string $countryCode, int $exceptUserId): bool
    {
        if ($normalizedContact === '') {
            return false;
        }

        return User::query()
            ->where('contact_number', $normalizedContact)
            ->whereIn('country_code', self::countryDialCodeVariants($countryCode))
            ->where('id', '!=', $exceptUserId)
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('is_register', 1)
                    ->orWhereNotNull('verified_at');
            })
            ->exists();
    }

    /**
     * OTP login can leave is_register=0 rows with the same phone; release before profile save.
     */
    public static function releaseContactFromIncompleteAccounts(string $normalizedContact, ?string $countryCode): void
    {
        if ($normalizedContact === '') {
            return;
        }

        User::query()
            ->where('contact_number', $normalizedContact)
            ->whereIn('country_code', self::countryDialCodeVariants($countryCode))
            ->where('is_register', 0)
            ->whereNull('deleted_at')
            ->update(['contact_number' => null]);
    }

    /**
     * Local 10-digit mobile for Colombia (+57 stored separately in country_code).
     */
    public static function normalizeColombianMobile(?string $phone, ?string $countryCode = null): string
    {
        $digits = self::normalizePhone($phone);
        if ($digits === '') {
            return '';
        }

        if (!self::isColombiaCountryCode($countryCode)) {
            return $digits;
        }

        $countryDigits = self::normalizePhone($countryCode);
        if ($countryDigits !== '' && str_starts_with($digits, $countryDigits) && strlen($digits) > 10) {
            $digits = substr($digits, strlen($countryDigits));
        }
        if (str_starts_with($digits, '57') && strlen($digits) > 10) {
            $digits = substr($digits, 2);
        }
        while (str_starts_with($digits, '0') && strlen($digits) > 10) {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        return $digits;
    }

    public static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $plate) ?? '');
    }

    public static function isValidColombianMobile(?string $phone, ?string $countryCode = null): bool
    {
        $digits = self::normalizeColombianMobile($phone, $countryCode);

        return (bool) preg_match('/^[0-9]{10}$/', $digits);
    }

    /**
     * E.164 destination for Twilio Verify (+573001234567 for Colombia).
     */
    public static function formatSmsDestination(?string $countryCode, ?string $contactNumber): ?string
    {
        if (self::isColombiaCountryCode($countryCode)) {
            $mobile = self::normalizeColombianMobile($contactNumber, $countryCode);
            if (! self::isValidColombianMobile($mobile, $countryCode)) {
                return null;
            }

            return '+57'.$mobile;
        }

        $dial = self::normalizeCountryDialCode($countryCode);
        $digits = self::normalizePhone($contactNumber);
        if ($digits === '') {
            return null;
        }

        $dialDigits = self::normalizePhone($dial);

        return '+'.$dialDigits.$digits;
    }

    public static function isValidColombianCarPlate(?string $plate): bool
    {
        $normalized = self::normalizePlate($plate);

        return (bool) preg_match('/^[A-Z]{3}[0-9]{3}$/', $normalized);
    }

    public static function isValidColombianMotoPlate(?string $plate): bool
    {
        $normalized = self::normalizePlate($plate);

        // Moto: ABC12D (3 letras + 2 números + 1 letra) o formato antiguo ABC123
        return (bool) preg_match('/^[A-Z]{3}[0-9]{2}[A-Z]$/', $normalized)
            || self::isValidColombianCarPlate($normalized);
    }

    /**
     * Moto-ratón / motoratón: placa libre (2–12 caracteres alfanuméricos).
     */
    public static function isOpenVehiclePlate(?string $plate): bool
    {
        $normalized = self::normalizePlate($plate);

        return $normalized !== '' && strlen($normalized) >= 2 && strlen($normalized) <= 12;
    }

    public static function vehicleServiceIdForType(?int $vehicleTypeId): ?int
    {
        if ($vehicleTypeId === null || $vehicleTypeId <= 0) {
            return null;
        }

        return (int) TransportVehicleType::query()
            ->where('id', $vehicleTypeId)
            ->value('service_id');
    }

    public static function isValidVehiclePlate(?string $plate, ?int $vehicleTypeId, ?int $serviceId = null): bool
    {
        $resolvedServiceId = $serviceId ?? self::vehicleServiceIdForType($vehicleTypeId);

        // Moto-ratón (motoratón) — placa abierta
        if ((int) $resolvedServiceId === 5) {
            return self::isOpenVehiclePlate($plate);
        }

        if ((int) $resolvedServiceId === 3) {
            return self::isValidColombianMotoPlate($plate);
        }

        return self::isValidColombianCarPlate($plate);
    }
}
