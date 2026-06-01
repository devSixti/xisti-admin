<?php
/**
 * Created by PhpStorm.
 * User: Froyo Khyati
 * Date: 27-May-19
 * Time: 10:43 AM
 */

namespace App\Classes;



use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TokenClassApi
{
    public function sendUserSmsVerification($user_id)
    {
        $user_details = User::query()->where('id', $user_id)->whereNull('deleted_at')->first();
        if ($user_details == Null) {
            return response()->json([
                "status" => 5,
                "message" => "Not Found!",
                "message_code" => 5,
            ]);
        }
        $settings = request()->get("general_settings");
        if ($settings == Null) {
            return response()->json([
                "status" => 0,
                "message" => "something went to wrong!",
                "message_code" => 9,
            ]);
        }
        if ($settings->is_otp_verification != Null && $settings->is_otp_verification == 1 && $user_details->is_default_user == 0) {
            if (isset($settings->otp_method)) {
                if ($settings->otp_method == 1){
                    if ($settings->twilio_service_key == Null || $settings->twilio_auth_token == Null || $settings->twilio_verify_service_key == Null) {
                        Log::warning('Twilio Verify settings missing for user OTP.', ['user_id' => $user_details->id]);
                        return response()->json([
                            "status" => 0,
                            "message" => "something went to wrong!",
                            "message_code" => 9,
                        ]);
                    }
                    try {
                        $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
                        $verification = $twilio->verify->v2->services($settings->twilio_verify_service_key)
                            ->verifications
                            ->create($user_details->country_code.$user_details->contact_number, "sms", ['locale' => "en"]);
                        $verification_sid = $verification->sid;
                        $get_token = UserVerification::query()->where('user_id', $user_details->id)->first();
                        if ($get_token != Null) {
                            UserVerification::query()->where('user_id', $user_details->id)->delete();
                        }
                        $new_otp = new UserVerification();
                        $new_otp->user_id = $user_details->id;
                        $new_otp->token = $verification_sid;
                        $new_otp->save();
                    } catch (\Exception $e) {
                        Log::error('Twilio Verify sendUserSmsVerification failed.', [
                            'user_id' => $user_details->id,
                            'error' => $e->getMessage(),
                        ]);
                        return response()->json([
                            'status' => 0,
                            'message' => __('user_messages.9'),
                            'message_code' => 9,
                        ]);
                    }
                }
            }
        }
        return "success";
    }
}
