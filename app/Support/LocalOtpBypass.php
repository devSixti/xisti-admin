<?php

namespace App\Support;

use App\Models\User;

/**
 * Fixed OTP 123456 only for seeded QA test users (never all users / never production-wide).
 */
class LocalOtpBypass
{
    public const FIXED_OTP = '123456';

    /** @deprecated Use QaTestUserHelper::isQaUser() */
    public static function isEnabled(): bool
    {
        return false;
    }

    public static function acceptsOtp(?User $user, ?string $otp): bool
    {
        return QaTestUserHelper::acceptsFixedOtp($user, $otp);
    }
}
