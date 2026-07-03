<?php

namespace App\Support;

use App\Classes\TokenClassApi;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

final class TwilioOtpVerification
{
    public static function verifyCode(User $user, UserVerification $record, string $code, $settings): bool
    {
        if ($settings->twilio_service_key === null
            || $settings->twilio_auth_token === null
            || $settings->twilio_verify_service_key === null) {
            return false;
        }

        $smsTo = ColombiaFormValidation::formatSmsDestination(
            $user->country_code,
            $user->contact_number
        );
        if ($smsTo === null) {
            return false;
        }

        $channel = OtpVerificationHelper::lastChannelForUser((int) $user->id, TokenClassApi::CHANNEL_SMS);
        $whatsappTo = 'whatsapp:'.$smsTo;
        $to = $channel === TokenClassApi::CHANNEL_WHATSAPP ? $whatsappTo : $smsTo;

        $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
        $serviceSid = $settings->twilio_verify_service_key;

        $attempts = [
            ['To' => $to, 'Code' => $code],
            ['To' => $smsTo, 'Code' => $code],
        ];

        $verificationSid = trim((string) $record->token);
        if ($verificationSid !== '' && ! in_array($verificationSid, ['qa-review-bypass', 'qa-test-bypass'], true)) {
            $attempts[] = ['To' => $to, 'VerificationSid' => $verificationSid, 'Code' => $code];
            $attempts[] = ['To' => $smsTo, 'VerificationSid' => $verificationSid, 'Code' => $code];
        }

        foreach ($attempts as $option) {
            try {
                $check = $twilio->verify->v2->services($serviceSid)->verificationChecks->create($option);
                if (($check->status ?? '') === 'approved') {
                    return true;
                }
            } catch (\Throwable $e) {
                Log::info('Twilio OTP verification attempt failed.', [
                    'user_id' => $user->id,
                    'to' => $option['To'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return false;
    }
}
