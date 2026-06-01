<?php

namespace App\Http\Controllers\Api\Auth;

use App\Classes\AdminClass;
use App\Classes\AuthAlertClass;
use App\Classes\NotificationClass;
use App\Classes\TokenClassApi;
use App\Classes\UserClassApi;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\ColombianMobileNumber;
use App\Support\ColombiaFormValidation;
use Intervention\Image\Laravel\Facades\Image;
use Twilio\Rest\Client;

class UpdateRegisterController extends Controller
{
//        json response status [
//            0 => false,
//            1 => true,
//            2 => registration pending,
//            3 => app user blocked,
//            4 => app user access token not match,
//            5 => app user not found
//          ]

    private $userClassApi;
    private $tokenClassApi;
    private $notificationClass;
    private $adminClass;

    public function __construct(TokenClassApi $tokenClassApi, UserClassApi $userClassApi, NotificationClass $notificationClass, AdminClass $adminClass)
    {
        $this->userClassApi = $userClassApi;
        $this->notificationClass = $notificationClass;
        $this->tokenClassApi = $tokenClassApi;
        $this->adminClass = $adminClass;
    }

    //customer
    public function postUpdateCustomerDetails(Request $request) {
        $countryCode = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
        $emergencyCountryCode = ColombiaFormValidation::normalizeCountryDialCode(
            $request->get('emergency_country_code') ?: $countryCode
        );
        $normalizedContact = ColombiaFormValidation::normalizeColombianMobile(
            $request->get('contact_number'),
            $countryCode
        );
        $normalizedEmergency = $request->get('emergency_contact') !== null && trim((string) $request->get('emergency_contact')) !== ''
            ? ColombiaFormValidation::normalizeColombianMobile($request->get('emergency_contact'), $emergencyCountryCode)
            : '';
        $request->merge([
            'select_country_code' => $countryCode,
            'emergency_country_code' => $emergencyCountryCode,
            'contact_number' => $normalizedContact,
            'emergency_contact' => $normalizedEmergency !== '' ? $normalizedEmergency : null,
        ]);

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required",
            "full_name" => "required",
            "profile_image" => "nullable",
            "email" => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) use ($request) {
                    $query->where('email', '=', $request->get('email'));
                    $query->where('id', '!=', $request->get('user_id'));
                    $query->where('deleted_at', '=', null);
                }),
            ],
            "select_country_code" => "required",
            "contact_number" => [
                'required',
                'numeric',
                new ColombianMobileNumber($countryCode),
            ],
            "emergency_contact" => ['nullable', 'numeric', new ColombianMobileNumber($emergencyCountryCode)],
            "emergency_contact_name" => 'nullable|string|max:120',
        ]);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['email']['Unique'])) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.11'),
                    "message_code" => 11,
                ]);
            }
            if ($validator->errors()->has('contact_number') || $validator->errors()->has('emergency_contact')) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.385'),
                    "message_code" => 385,
                ]);
            }
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        ColombiaFormValidation::releaseContactFromIncompleteAccounts($normalizedContact, $countryCode);

        if (ColombiaFormValidation::contactTakenByOtherUser(
            $normalizedContact,
            $countryCode,
            (int) $request->get('user_id')
        )) {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.12'),
                "message_code" => 12,
            ]);
        }

        $user_details = $this->userClassApi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassApi->authJsonResponse($user_details)) {
            return $failed;
        }
        $user_details->first_name = $request->get('full_name');
        $user_details->contact_number = $normalizedContact;
        $user_details->country_code = $countryCode;
        $user_details->email = $request->get('email');
        if ($request->file('profile_image') != Null) {
            if (\File::exists(public_path('/assets/images/profile-images/customer/' . $user_details->avatar))) {
                \File::delete(public_path('/assets/images/profile-images/customer/' . $user_details->avatar));
            }
            $destinationPath = public_path('/assets/images/profile-images/customer/');
            $file = $request->file('profile_image');
            $img = Image::read($file->getRealPath());
            $img->orient();
            $file_new = rand(1, 9) . date('siHYdm') . rand(1, 9) . '.' . $file->getClientOriginalExtension();
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . $file_new);
            $user_details->avatar = $file_new;
        }
        $user_details->emergency_contact = $normalizedEmergency !== '' ? $normalizedEmergency : null;
        $user_details->emergency_country_code = $normalizedEmergency !== '' ? $emergencyCountryCode : null;
        if ($request->has('emergency_contact_name')) {
            $name = trim((string) $request->get('emergency_contact_name'));
            $user_details->emergency_contact_name = $name !== '' ? $name : null;
        }
        if ($user_details->is_register != 1) {
            $user_details->is_register = 1;
        }
        $user_details->save();

        return $this->userClassApi->userLoginRegisterUpdateDetails($user_details);
    }

    public function postCustomerUpdateCountryAndCurrency(Request $request) {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required",
            "select_language" => "required",
            "select_country_code" => "required",
            "select_currency" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_details = $this->userClassApi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassApi->authJsonResponse($user_details)) {
            return $failed;
        }
        $user_details->country_code = $request->get('select_country_code');
        $user_details->currency = $request->get('select_currency');
        $user_details->language = $request->get('select_language');
        $user_details->save();
        return response()->json([
            'status' => 1,
            //'message' => "success!",
            'message' => __('user_messages.1'),
            'message_code' => 1,
        ]);
    }

    //user
    public function postCustomerResendOtpVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user = User::query()->where('id', $request->get('user_id'))->whereNull('deleted_at')->first();
        if ($user == Null) {
            return response()->json([
                'status' => 5,
                //'message' => "User not found!",
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        if ($user->access_token != $request->get("access_token")) {
            return response()->json([
                'status' => 4,
                //'message' => "Access Token Not Match!",
                'message' => __('user_messages.4'),
                'message_code' => 4,
            ]);
        }
        if ($user->verified_at == Null) {
            $this->tokenClassApi->sendUserSmsVerification($user->id);
        }
        return response()->json([
            "status" => 1,
            //"message" => "success",
            "message" => __('user_messages.1'),
            "message_code" => 1,
        ]);
    }

    public function postCustomerContactVerification(Request $request) {
        $check_authentication = (new AuthAlertClass())->checkAuthorizationApp($request);
        if ($check_authentication->getData()->status != 1){
            return $check_authentication;
        }

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required|numeric",
            "otp" => "required|numeric|digits:6"
        ]);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['otp']['Digits'])) {
                return response()->json([
                    'status' => 0,
                    //'message' => "Invalid Otp!",
                    'message' => __('user_messages.89'),
                    'message_code' => 89,
                ]);
            }
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $user_details = User::query()->where('id', $request->get('user_id'))->whereNull('deleted_at')->first();
        if ($user_details != Null) {
            if ($user_details->access_token != $request->get('access_token')) {
                return response()->json([
                    'status' => 4,
                    //'message' => "Access Token Not Match!",
                    'message' => __('user_messages.4'),
                    'message_code' => 4,
                ]);
            }
            $settings = request()->get('general_settings');
            if ($settings == Null) {
                return response()->json([
                    "status" => 0,
                    //"message" => "something went to wrong!",
                    "message" => __('user_messages.9'),
                    "message_code" => 9,
                ]);
            }
            if ($settings->is_otp_verification > 0 && $user_details->is_default_user == 0 && $user_details->fix_user_show == 0) {
                if (isset($settings->otp_method)) {
                    if ($settings->otp_method == 1){
                        info($user_details->id);
                        $get_otp = UserVerification::query()->where('user_id', "=", $user_details->id)->first();
                        if ($get_otp == Null){
                            info("step 1");
                            return response()->json([
                                "status" => 0,
                                "message" => __('user_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                        try {
                            if ($settings->twilio_service_key == Null || $settings->twilio_auth_token == Null || $settings->twilio_verify_service_key == Null) {
                                info("step 2");
                                return response()->json([
                                    "status" => 0,
                                    "message" => __('user_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                            $twilio = new Client($settings->twilio_service_key, $settings->twilio_auth_token);
                            $option = [
                                'To' => $user_details->country_code.$user_details->contact_number,
                                'VerificationSid' => $get_otp->token,
                                'Code' => $request->get('otp'),
                            ];
                            $verification_check = $twilio->verify->v2->services($settings->twilio_verify_service_key)->verificationChecks->create($option);
                            if ($verification_check->status == "approved") {
                                $verification_sid = $verification_check->sid;
                                info($verification_sid);
                                info($verification_check->sid);
                                if ($verification_sid != $get_otp->token) {
                                    return response()->json([
                                        "status" => 0,
                                        "message" => __('user_messages.9'),
                                        "message_code" => 9,
                                    ]);
                                }
                                UserVerification::query()->where('user_id', "=", $user_details->id)->delete();
                                $user_details->verified_at = date('Y-m-d H:i:s');
                                $user_details->device_token = $request->device_token ?? null;
                                $user_details->save();
                            } else {
                                return response()->json([
                                    "status" => 0,
                                    "message" => __('user_messages.89'),
                                    "message_code" => 89,
                                ]);
                            }
                        } catch (\Exception $e) {
                            return response()->json([
                                "status" => 0,
                                "message" => __('user_messages.89'),
                                "message_code" => 89,
                            ]);
                        }
                        //} elseif ($settings->otp_method == 2) {
                    }
                    else{
                        return response()->json([
                            "status" => 0,
                            //"message" => "Verify method does not exists!",
                            "message" => __('user_messages.9'),
                            "message_code" => 9,
                        ]);
                    }
                } else {
                    return response()->json([
                        "status" => 0,
                        "message" => "Verify method does not exists",
                        "message_code" => 9,
                    ]);
                }
            } else {
                if ($request->get('otp') == "123456") {
                    $user_details->verified_at = date('Y-m-d H:i:s');
                    $user_details->save();
                } else {
                    return response()->json([
                        "status" => 0,
                        //"message" => "Invalid Otp!",
                        "message" => __('user_messages.89'),
                        "message_code" => 89,
                    ]);
                }
            }
            return $this->userClassApi->userLoginRegisterUpdateDetails($user_details);
        } else {
            return response()->json([
                "status" => 5,
                //"message" => "user not found!",
                "message" => __('user_messages.5'),
                "message_code" => 5,
            ]);
        }
    }

    public function postCustomerChangeContactNumber(Request $request)
    {
        $countryCode = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
        $normalizedContact = ColombiaFormValidation::normalizeColombianMobile(
            $request->get('contact_number'),
            $countryCode
        );
        $request->merge([
            'select_country_code' => $countryCode,
            'contact_number' => $normalizedContact,
        ]);

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required",
            "contact_number" => [
                "required",
                "numeric",
                new ColombianMobileNumber($countryCode),
            ],
            "select_country_code" => "required"
        ]);
        if ($validator->fails()) {
            if ($validator->errors()->has('contact_number')) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.385'),
                    "message_code" => 385,
                ]);
            }
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        ColombiaFormValidation::releaseContactFromIncompleteAccounts($normalizedContact, $countryCode);

        if (ColombiaFormValidation::contactTakenByOtherUser(
            $normalizedContact,
            $countryCode,
            (int) $request->get('user_id')
        )) {
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.12'),
                "message_code" => 12,
            ]);
        }

        $user_details = User::query()->where('id', $request->get('user_id'))->whereNull('deleted_at')->first();
        if ($user_details != Null) {
            if ($user_details->access_token != $request->get('access_token')) {
                return response()->json([
                    'status' => 4,
//                    'message' => "Access Token Not Match!",
                    'message' => __('user_messages.4'),
                    "message_code" => 4,
                ]);
            }
            if ($user_details->status == 0) {
                return response()->json([
                    'status' => 3,
//                    'message' => 'Your account is currently blocked, so not authorised to allow any activity!',
                    'message' => __('user_messages.3'),
                    "message_code" => 3,
                ]);
            }
            $user_details->contact_number = $normalizedContact;
            $user_details->country_code = $countryCode;
            $user_details->save();
            $this->tokenClassApi->sendUserSmsVerification($user_details->id);
            return response()->json([
                'status' => 1,
                'message' => __('user_messages.1'),
                "message_code" => 1,
                'contact_number' => $user_details->contact_number,
                "select_country_code" => $user_details->country_code,
            ]);
        } else {
            return response()->json([
                'status' => 5,
//                'message' => "User not found!",
                'message' => __('user_messages.5'),
                "message_code" => 5,
            ]);
        }
    }
}
