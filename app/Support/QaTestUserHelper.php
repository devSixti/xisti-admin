<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\XistiQaTestUserSeeder;

/**
 * QA test accounts that may use fixed phone + OTP (123456) without Twilio.
 */
class QaTestUserHelper
{
    public static function isQaUser(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ((int) ($user->fix_user_show ?? 0) === 1 && (int) ($user->is_default_user ?? 0) === 1) {
            return true;
        }

        $phone = preg_replace('/\D+/', '', (string) $user->contact_number);
        $country = trim((string) $user->country_code);
        $qaPhone = XistiQaTestUserSeeder::QA_PHONE_LOCAL;
        $qaCountry = ltrim(XistiQaTestUserSeeder::QA_COUNTRY_CODE, '+');

        return $phone === $qaPhone
            && in_array($country, [XistiQaTestUserSeeder::QA_COUNTRY_CODE, '+'.$qaCountry, $qaCountry], true);
    }

    public static function acceptsFixedOtp(?User $user, ?string $otp): bool
    {
        return self::isQaUser($user) && $otp === LocalOtpBypass::FIXED_OTP;
    }
}
