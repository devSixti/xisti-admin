<?php

namespace App\Support;

use Illuminate\Http\Request;

class SignupPhoneOtpHelper
{
    public const REQUEST_HEADER = 'X-Signup-Phone-Otp';

    public static function clientRequiresSignupPhoneOtp(Request $request): bool
    {
        return $request->header(self::REQUEST_HEADER) === '1';
    }
}
