<?php

namespace App\Classes;

use App\Models\User;
use App\Models\UserVerification;
use App\Support\ColombiaFormValidation;
use App\Support\QaTestUserHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Twilio\Rest\Client;

class TokenClassApi
{
    public const CHANNEL_SMS = 'sms';

    public const CHANNEL_WHATSAPP = 'whatsapp';

    public function sendUserSmsVerification($user_id, string $channel = 'sms')
    {
        $channel = in_array($channel, [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP], true)
            ? $channel
            : self::CHANNEL_SMS;
        $user_details = User::query()->where('id', $user_id)->whereNull('deleted_at')->first();
        if ($user_details == Null) {
            return response()->json([
                'status' => 5,
                'message' => 'Not Found!',
                'message_code' => 5,
            ]);
        }
        if (QaTestUserHelper::isQaUser($user_details)) {
            UserVerification::query()->where('user_id', $user_details->id)->delete();
            $localOtp = new UserVerification();
            $localOtp->user_id = $user_details->id;
            $localOtp->token = 'qa-test-bypass';
            $this->assignVerificationChannel($localOtp, self::CHANNEL_SMS);
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

                $channels = $channel === self::CHANNEL_WHATSAPP
                    ? [self::CHANNEL_WHATSAPP]
                    : [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP];

                return $this->dispatchTwilioVerify($user_details, $settings, $channels);
            }
        }

        return 'success';
    }

    public static function lastChannelForUser(int $userId): string
    {
        if (! Schema::hasColumn('user_verification', 'verification_channel')) {
            return self::CHANNEL_SMS;
        }

        $channel = UserVerification::query()
            ->where('user_id', $userId)
            ->value('verification_channel');

        return in_array($channel, [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP], true)
            ? $channel
            : self::CHANNEL_SMS;
    }

    private function dispatchTwilioVerify(User $user_details, $settings, array $channels)
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

        $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
        $serviceSid = $settings->twilio_verify_service_key;
        $lastException = null;

        foreach ($channels as $index => $channel) {
            try {
                $this->cancelStoredPendingVerification($twilio, $serviceSid, (int) $user_details->id);

                $verification = $twilio->verify->v2->services($serviceSid)
                    ->verifications
                    ->create($phone, $channel, ['locale' => 'es']);

                $this->persistVerificationRecord((int) $user_details->id, $verification->sid, $channel);

                if ($index > 0) {
                    Log::info('XISTI OTP delivered via WhatsApp after SMS fallback.', [
                        'user_id' => $user_details->id,
                    ]);
                }

                return 'success';
            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning('Twilio Verify OTP channel failed.', [
                    'user_id' => $user_details->id,
                    'channel' => $channel,
                    'error' => $e->getMessage(),
                ]);
                if ($this->isTwilioVerifyRateLimit($e)) {
                    break;
                }
                if ($index === 0 && count($channels) > 1 && $this->shouldFallbackSmsToWhatsapp($e)) {
                    continue;
                }
                break;
            }
        }

        Log::error('Twilio Verify sendUserSmsVerification failed.', [
            'user_id' => $user_details->id,
            'error' => $lastException?->getMessage(),
            'rate_limited' => $lastException !== null && $this->isTwilioVerifyRateLimit($lastException),
        ]);

        return response()->json([
            'status' => 0,
            'message' => __('user_messages.9'),
            'message_code' => 9,
        ]);
    }

    private function cancelStoredPendingVerification(Client $twilio, string $serviceSid, int $userId): void
    {
        $stored = UserVerification::query()->where('user_id', $userId)->first();
        if ($stored === null || trim((string) $stored->token) === '') {
            return;
        }

        $verificationSid = trim((string) $stored->token);
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
        $this->assignVerificationChannel($record, $channel);
        $record->save();
    }

    private function assignVerificationChannel(UserVerification $record, string $channel): void
    {
        if (Schema::hasColumn('user_verification', 'verification_channel')) {
            $record->verification_channel = $channel;
        }
    }

    private function isTwilioVerifyRateLimit(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, '60203')
            || str_contains($message, 'max send attempts')
            || str_contains($message, 'too many requests');
    }

    private function shouldFallbackSmsToWhatsapp(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, '21608')
            || str_contains($message, '30003')
            || str_contains($message, '30004')
            || str_contains($message, '30005')
            || str_contains($message, '30006')
            || str_contains($message, '30007')
            || str_contains($message, '30008')
            || str_contains($message, 'blocked')
            || str_contains($message, 'undelivered')
            || str_contains($message, 'unreachable')
            || str_contains($message, 'carrier');
    }
}
