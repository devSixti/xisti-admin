<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\XistiQaTestUserSeeder;

/**
 * QA test accounts that may use fixed phone + OTP (123456) without Twilio.
 * Restricted to explicit QA phone numbers only.
 */
class QaTestUserHelper
{
    /** @return list<string> */
    public static function qaPhoneLocals(): array
    {
        return [
            XistiQaTestUserSeeder::QA_PHONE_LOCAL,
            XistiQaTestUserSeeder::QA_DRIVER_PHONE_LOCAL,
        ];
    }

    public static function isQaUser(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $phone = preg_replace('/\D+/', '', (string) $user->contact_number);
        if ($phone === '' || ! in_array($phone, self::qaPhoneLocals(), true)) {
            return false;
        }

        $country = trim((string) $user->country_code);
        $qaCountry = ltrim(XistiQaTestUserSeeder::QA_COUNTRY_CODE, '+');

        return in_array($country, [XistiQaTestUserSeeder::QA_COUNTRY_CODE, '+'.$qaCountry, $qaCountry], true);
    }

    public static function acceptsFixedOtp(?User $user, ?string $otp): bool
    {
        return self::isQaUser($user) && $otp === LocalOtpBypass::FIXED_OTP;
    }
}
