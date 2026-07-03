<?php

namespace App\Classes;

use App\Models\User;
use App\Models\UserVerification;
use App\Support\ColombiaFormValidation;
use App\Support\OtpVerificationHelper;
use App\Support\QaTestUserHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Twilio\Rest\Client;

class TokenClassApi
{
    public const CHANNEL_SMS = 'sms';

    public const CHANNEL_WHATSAPP = 'whatsapp';

    public function sendUserSmsVerification($user_id, string $channel = 'sms', bool $forceResend = false)
    {
        $channel = in_array($channel, [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP], true)
            ? $channel
            : self::CHANNEL_SMS;
        $user_details = User::query()->where('id', $user_id)->whereNull('deleted_at')->first();
        if ($user_details == Null) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found!',
                'message_code' => 5,
            ]);
        }
        if (QaTestUserHelper::isQaUser($user_details)) {
            UserVerification::query()->where('user_id', $user_details->id)->delete();
            $localOtp = new UserVerification();
            $localOtp->user_id = $user_details->id;
            $localOtp->token = 'qa-test-bypass';
            OtpVerificationHelper::assignChannel($localOtp, self::CHANNEL_SMS);
            OtpVerificationHelper::markSent($localOtp);
            $localOtp->save();
            Log::info('XISTI QA OTP bypass: code issued without Twilio.', ['user_id' => $user_details->id]);

            return 'success';
        }
        $settings = \App\Support\RequestSettingsHelper::generalSettings();
        if ($settings == Null) {
            return response()->json([
                'status' => 0,
                'message' => 'something went to wrong!',
                'message_code' => 9,
            ]);
        }
        if ($settings->is_otp_verification != Null && $settings->is_otp_verification == 1 && $user_details->is_default_user == 0) {
            if (isset($settings->otp_method) && (int) $settings->otp_method === 1) {
                if ($settings->twilio_service_key == Null || $settings->twilio_auth_token == Null || $settings->twilio_verify_service_key == Null) {
                    Log::warning('Twilio Verify settings missing for user OTP.', ['user_id' => $user_details->id]);

                    return response()->json([
                        'status' => 0,
                        'message' => 'something went to wrong!',
                        'message_code' => 9,
                    ]);
                }

                if (! $forceResend && OtpVerificationHelper::hasRecentPending((int) $user_details->id)) {
                    Log::info('OTP send skipped: recent verification still active.', [
                        'user_id' => $user_details->id,
                        'channel' => OtpVerificationHelper::lastChannelForUser((int) $user_details->id),
                    ]);

                    return 'success';
                }

                return $this->dispatchTwilioVerify($user_details, $settings, $channel);
            }
        }

        return 'success';
    }

    public static function lastChannelForUser(int $userId): string
    {
        return OtpVerificationHelper::lastChannelForUser($userId, self::CHANNEL_SMS);
    }

    private function dispatchTwilioVerify(User $user_details, $settings, string $channel)
    {
        $phone = ColombiaFormValidation::formatSmsDestination(
            $user_details->country_code,
            $user_details->contact_number
        );
        if ($phone === null) {
            Log::warning('Invalid SMS destination for user OTP.', [
                'user_id' => $user_details->id,
                'country_code' => $user_details->country_code,
                'contact_number' => $user_details->contact_number,
            ]);

            return response()->json([
                'status' => 0,
                'message' => __('user_messages.385'),
                'message_code' => 385,
            ]);
        }

        try {
            $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
            $serviceSid = $settings->twilio_verify_service_key;

            $this->cancelStoredPendingVerification($twilio, $serviceSid, (int) $user_details->id);

            $verification = $twilio->verify->v2->services($serviceSid)
                ->verifications
                ->create($phone, $channel, ['locale' => 'es']);

            $this->persistVerificationRecord((int) $user_details->id, $verification->sid, $channel);

            return 'success';
        } catch (\Exception $e) {
            Log::error('Twilio Verify sendUserSmsVerification failed.', [
                'user_id' => $user_details->id,
                'channel' => $channel,
                'error' => $e->getMessage(),
                'rate_limited' => $this->isTwilioVerifyRateLimit($e),
            ]);

            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ]);
        }
    }

    private function cancelStoredPendingVerification(Client $twilio, string $serviceSid, int $userId): void
    {
        $stored = UserVerification::query()->where('user_id', $userId)->first();
        if ($stored === null || trim((string) $stored->token) === '') {
            return;
        }

        $verificationSid = trim((string) $stored->token);
        if (in_array($verificationSid, ['qa-review-bypass', 'qa-test-bypass'], true)) {
            return;
        }

        try {
            $current = $twilio->verify->v2->services($serviceSid)
                ->verifications($verificationSid)
                ->fetch();

            if (($current->status ?? '') === 'pending') {
                $twilio->verify->v2->services($serviceSid)
                    ->verifications($verificationSid)
                    ->update('canceled');

                Log::info('Canceled pending Twilio verification before sending a new OTP.', [
                    'user_id' => $userId,
                    'verification_sid' => $verificationSid,
                    'channel' => $current->channel ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::info('No active Twilio verification to cancel before OTP resend.', [
                'user_id' => $userId,
                'verification_sid' => $verificationSid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function persistVerificationRecord(int $userId, string $verificationSid, string $channel): void
    {
        UserVerification::query()->where('user_id', $userId)->delete();
        $record = new UserVerification();
        $record->user_id = $userId;
        $record->token = $verificationSid;
        OtpVerificationHelper::assignChannel($record, $channel);
        OtpVerificationHelper::markSent($record);
        $record->save();
    }

    private function isTwilioVerifyRateLimit(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, '60203')
            || str_contains($message, 'max send attempts')
            || str_contains($message, 'too many requests');
    }
}
