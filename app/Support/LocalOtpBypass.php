<?php

namespace App\Support;

/**
 * QA / local OTP bypass — fixed code 123456 without Twilio.
 * Enable with APP_ENV=local or XISTI_OTP_BYPASS=1 on the server.
 */
class LocalOtpBypass
{
    public const FIXED_OTP = '123456';

    public static function isEnabled(): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        return filter_var(config('xisti.otp_bypass', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function acceptsOtp(?string $otp): bool
    {
        return $otp === self::FIXED_OTP;
    }
}
