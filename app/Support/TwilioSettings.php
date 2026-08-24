<?php

namespace App\Support;

/**
 * Resolves Twilio Verify credentials: env/config first, then general_settings.
 * Never log full tokens.
 */
final class TwilioSettings
{
    public static function accountSid(?object $settings = null): ?string
    {
        return self::firstNonEmpty(
            config('services.twilio.account_sid'),
            $settings->twilio_service_key ?? null,
        );
    }

    public static function authToken(?object $settings = null): ?string
    {
        return self::firstNonEmpty(
            config('services.twilio.auth_token'),
            $settings->twilio_auth_token ?? null,
        );
    }

    public static function verifyServiceSid(?object $settings = null): ?string
    {
        return self::firstNonEmpty(
            config('services.twilio.verify_service_sid'),
            $settings->twilio_verify_service_key ?? null,
        );
    }

    public static function isConfigured(?object $settings = null): bool
    {
        return self::accountSid($settings) !== null
            && self::authToken($settings) !== null
            && self::verifyServiceSid($settings) !== null;
    }

    /**
     * Overlay resolved credentials onto the settings object in memory (not persisted).
     */
    public static function hydrate(object $settings): object
    {
        $settings->twilio_service_key = self::accountSid($settings);
        $settings->twilio_auth_token = self::authToken($settings);
        $settings->twilio_verify_service_key = self::verifyServiceSid($settings);

        return $settings;
    }

    public static function sidPrefix(?object $settings = null): string
    {
        $sid = (string) (self::accountSid($settings) ?? '');

        return $sid !== '' ? substr($sid, 0, 6).'…' : '(missing)';
    }

    private static function firstNonEmpty(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            $trimmed = trim((string) ($value ?? ''));
            if ($trimmed !== '') {
                return $trimmed;
            }
        }

        return null;
    }
}
