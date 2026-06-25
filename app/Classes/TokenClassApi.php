<?php

namespace App\Classes;

use App\Models\User;
use App\Models\UserVerification;
use App\Support\QaTestUserHelper;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TokenClassApi
{
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_WHATSAPP = 'whatsapp';

    public function sendUserSmsVerification($user_id)
    {
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
            $localOtp->verification_channel = self::CHANNEL_SMS;
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

                return $this->dispatchTwilioVerify($user_details, $settings);
            }
        }

        return 'success';
    }

    public static function lastChannelForUser(int $userId): string
    {
        $channel = UserVerification::query()
            ->where('user_id', $userId)
            ->value('verification_channel');

        return in_array($channel, [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP], true)
            ? $channel
            : self::CHANNEL_SMS;
    }

    private function dispatchTwilioVerify(User $user_details, $settings)
    {
        $phone = $user_details->country_code.$user_details->contact_number;
        $channels = [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP];
        $lastException = null;

        foreach ($channels as $index => $channel) {
            try {
                $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
                $verification = $twilio->verify->v2->services($settings->twilio_verify_service_key)
                    ->verifications
                    ->create($phone, $channel, ['locale' => 'es']);
                UserVerification::query()->where('user_id', $user_details->id)->delete();
                $new_otp = new UserVerification();
                $new_otp->user_id = $user_details->id;
                $new_otp->token = $verification->sid;
                $new_otp->verification_channel = $channel;
                $new_otp->save();

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
                if ($index === 0 && $this->shouldFallbackSmsToWhatsapp($e)) {
                    continue;
                }
                break;
            }
        }

        Log::error('Twilio Verify sendUserSmsVerification failed.', [
            'user_id' => $user_details->id,
            'error' => $lastException?->getMessage(),
        ]);

        return response()->json([
            'status' => 0,
            'message' => __('user_messages.9'),
            'message_code' => 9,
        ]);
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
