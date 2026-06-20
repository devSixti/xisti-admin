<?php

namespace App\Http\Controllers\Api\Auth;


use App\Classes\AuthAlertClass;
use App\Classes\NotificationClass;
use App\Classes\TokenClassApi;
use App\Classes\UserClassApi;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Rules\ColombianMobileNumber;
use App\Support\ColombiaFormValidation;
use App\Support\SignupPhoneOtpHelper;

class LoginController extends Controller
{
    //json response status [
    //    0 => false,
    //    1 => true,
    //    2 => registration pending,
    //    3 => app user blocked,
    //    4 => app user access token not match,
    //    5 => app user not found
    //  ]

    //ApiLogDetail logger type => 0:user,1:store,2:driver,3:provider
    private $userClassApi;
    Private $tokenClassApi;
    private $notificationClass;

    public function __construct(TokenClassApi $tokenClassApi, UserClassApi $userClassApi, NotificationClass $notificationClass)
    {
        $this->userClassApi = $userClassApi;
        $this->tokenClassApi = $tokenClassApi;
        $this->notificationClass = $notificationClass;
    }


    public function postCustomerLogin(Request $request) {
        $check_authentication = (new AuthAlertClass())->checkAuthorizationApp($request);
        if ($check_authentication->getData()->status != 1){
            return $check_authentication;
        }

        $validator = Validator::make($request->all(), [
            'login_type' => 'required|in:facebook,google,email,apple',
            'device_token' => 'required',
            'select_language' => 'required',
            'select_country_code' => 'nullable',
            'select_currency' => 'required',
            'login_device' => 'nullable|in:1,2'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }
        $login_type = $request->get('login_type');
        $requiresSignupPhoneOtp = false;

        if ($login_type != "facebook" && $login_type != "google" && $login_type != "apple") {
            $validator = Validator::make($request->all(), [
                'contact_number' => [
                    'required',
                    'numeric',
                    new ColombianMobileNumber($request->get('select_country_code')),
                ],
                'select_country_code' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                    'message_code' => 9,
                ]);
            }
            $countryCode = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
            $normalizedContact = ColombiaFormValidation::normalizeColombianMobile(
                $request->get('contact_number'),
                $countryCode
            );
            ColombiaFormValidation::releaseContactFromIncompleteAccounts($normalizedContact, $countryCode);

            $user_details = User::query()
                ->where('contact_number', '=', $normalizedContact)
                ->whereIn('country_code', ColombiaFormValidation::countryDialCodeVariants($countryCode))
                ->whereNull('deleted_at')
                ->orderByDesc('is_register')
                ->orderByRaw('verified_at IS NOT NULL DESC')
                ->orderByDesc('id')
                ->first();
            if ($user_details != Null) {
                if ($user_details->status == 0) {
                    return response()->json([
                        'status' => 3,
                        'message' => __('user_messages.3'),
                        'message_code' => 3,
                    ]);
                }
            } else {
                $user_details = new User();
                $user_details->login_type = "email";
                $user_details->contact_number = $normalizedContact;
                $user_details->status = 1;
                $user_details->is_register = 0;
            }
            $user_details->country_code = $countryCode;
            if ($user_details->contact_number === null || $user_details->contact_number === '') {
                $user_details->contact_number = $normalizedContact;
            }
            $user_details->verified_at = Null;
            $user_details->save();
            $this->tokenClassApi->sendUserSmsVerification($user_details->id);
        }
        else {
            // social login
            $validator = Validator::make($request->all(), [
                'login_id' => 'required',
                'contact_number' => 'nullable',
                'full_name' => 'required',
                'profile_image' => 'nullable',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                    'message_code' => 9,
                ]);
            }

            $login_id = $request->get('login_id');
            $requiresSignupPhoneOtp = SignupPhoneOtpHelper::clientRequiresSignupPhoneOtp($request);
            $user_details = User::query()->where('login_type','=', $login_type)->where('login_id', '=', $login_id)->whereNull('deleted_at')->first();
            // Returning social accounts already registered must not be forced through phone OTP again.
            if ($user_details !== null && (int) $user_details->is_register === 1) {
                $requiresSignupPhoneOtp = false;
            }
            if ($user_details == Null) {
                $contact_number = $request->get("contact_number");
                $is_update = 0;
                $user_id = 0;
                if ($contact_number != Null){
                    if (!is_numeric($contact_number)) {
                        $check_email = User::query()->where('email', $contact_number)->whereNull('deleted_at')->first();
                        if ($check_email != Null){
                            if ($check_email->login_type != "email"){
                                return response()->json([
                                    'status' => 0,
                                    //'message' => 'Email Already Exist!',
                                    'message' => __('user_messages.11'),
                                    'message_code' => 11,
                                ]);
                            } else {
                                $is_update = 1;
                                $user_id = $check_email->id;
                            }
                        }
                    }
                }
                $is_new_or_not = 0;
                if ($is_update == 1){
                    $user_details = User::query()->where('id','=', $user_id)->whereNull('deleted_at')->first();
                    if ($user_details == Null){
                        $user_details = new User();
                        $is_new_or_not = 1;
                    }
                    if ($user_details->status == 0) {
                        return response()->json([
                            'status' => 3,
                            //'message' => 'Your account is currently blocked, so not authorised to allow any activity!',
                            'message' => __('user_messages.3'),
                            'message_code' => 3,
                        ]);
                    }
                } else{
                    $user_details = new User();
                    $is_new_or_not = 1;
                }
                $user_details->is_register = $requiresSignupPhoneOtp ? 0 : 1;
                $user_details->login_type = $login_type;
                $user_details->login_id = $login_id;
                $user_details->verified_at = $requiresSignupPhoneOtp ? null : date('Y-m-d H:i:s');
                $user_details->save();

                if ($is_new_or_not == 1) {
                    $user_details->status = 1;
                    $user_details->is_register = $requiresSignupPhoneOtp ? 0 : 1;

                    if (filter_var($request->get('profile_image'), FILTER_VALIDATE_URL) == true) {
                        $user_details->avatar = $request->get('profile_image');
                    }
                    if (trim($request->get("full_name")) != "N/A") {
                        $user_details->first_name = ucwords(strtolower(trim($request->get('full_name'))));
                    }
                    if ($contact_number != Null) {
                        if (is_numeric($contact_number)) {
                            $phoneValidator = Validator::make($request->all(), [
                                'contact_number' => [
                                    'required',
                                    'numeric',
                                    new ColombianMobileNumber($request->get('select_country_code')),
                                ],
                            ]);
                            if ($phoneValidator->fails()) {
                                return response()->json([
                                    'status' => 0,
                                    'message' => __('user_messages.385'),
                                    'message_code' => 385,
                                ]);
                            }
                            $socialCountry = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
                            $normalizedSocialContact = ColombiaFormValidation::normalizeColombianMobile(
                                $contact_number,
                                $socialCountry
                            );
                            ColombiaFormValidation::releaseContactFromIncompleteAccounts(
                                $normalizedSocialContact,
                                $socialCountry
                            );
                            $user_details->contact_number = $normalizedSocialContact;
                            $user_details->country_code = $socialCountry;
                        } else {
                            $user_details->email = $contact_number;
                        }
                    }
                    $user_details->save();
                    //sending mail
                    $general_setting =request()->get("general_settings") ;
                    if ($general_setting != Null){
                        if ($general_setting->send_mail == 1) {
                            $notificationClass = new NotificationClass();
                            if($user_details->email != null){
                                try {
                                    $mail_type = "customer_signup";
                                    $to_mail = $user_details->email;
                                    $subject = "Welcome to " . $general_setting->mail_site_name;
                                    $disp_data = array("##user_name##" => $user_details->first_name );
                                    $mail_return_data = $notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                } catch (\Exception $e) {
                                    \Log::info('Exception');
                                    \Log::info($e);
                                }
                            }
                            if ($general_setting->send_receive_email != Null ){
                                try {
                                    $mail_type = "admin_new_user_signup";
                                    $to_mail = $general_setting->send_receive_email;
                                    $subject = "New User has Register";
                                    $disp_data = array("##user_name##" => $user_details->first_name , "##mail_site_name##" => $general_setting->mail_site_name);
                                    $mail_return_data = $notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                } catch (\Exception $e) {}
                            }
                        }
                    }
                }
                if ($is_new_or_not == 1) {
                    if ($user_details->first_name != Null) {
                        $user_details->InviteCode($user_details->id, $user_details->first_name);
                    }
                }
            }
        else {
                if ($user_details->status == 0) {
                    return response()->json([
                        'status' => 3,
                        //'message' => 'Your account is currently blocked, so not authorised to allow any activity!',
                        'message' => __('user_messages.3'),
                        'message_code' => 3,
                    ]);
                }
                if ((int) $user_details->is_register === 1) {
                    if ($user_details->verified_at === null) {
                        $user_details->verified_at = date('Y-m-d H:i:s');
                        $user_details->save();
                    }
                } elseif (!$requiresSignupPhoneOtp) {
                    $user_details->verified_at = date('Y-m-d H:i:s');
                    $user_details->save();
                }
            }
        }
        if ($request->get('select_country_code') != NULL){
            $user_details->country_code = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
        }
        $user_details->currency = $request->get('select_currency');
        $user_details->language = $request->get('select_language');
        $user_details->device_token = $request->get('device_token');
        $user_details->login_device = $request->get('login_device') != Null ? $request->get('login_device') : 0;
        $user_details->save();

        if (
            in_array($login_type, ['google', 'facebook', 'apple'], true)
            && $requiresSignupPhoneOtp
            && $user_details->verified_at === null
            && ! empty($user_details->contact_number)
        ) {
            $this->tokenClassApi->sendUserSmsVerification($user_details->id);
        }

        $user_details->refreshAccessTokenForDevice($request->get('device_token'));
        return $this->userClassApi->userLoginRegisterUpdateDetails($user_details);
    }

    public function postCustomerFingerLogin(Request $request)
    {
        $check_authentication = (new AuthAlertClass())->checkAuthorizationApp($request);
        if ($check_authentication->getData()->status != 1){
            return $check_authentication;
        }

        $validator = Validator::make($request->header(), [
            'select-time-zone' => 'required',
            'select-ip-address' => 'nullable',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'login_type' => 'required|in:fingerprint',
            'device_token' => 'required',
            'select_language' => 'required',
            'select_country_code' => 'nullable',
            'select_currency' => 'required',
            'login_device' => 'nullable|in:1,2'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $login_type = $request->get('login_type');
        if($login_type == "fingerprint") {
            //code for fingerprint login
            $validator = Validator::make($request->all(), [
                'login_id' => 'required',
                'unique_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                    'message_code' => 9,
                ]);
            }

            $user_details = User::query()->where('id', '=', $request->get('login_id'))->where('unique_id', '=', $request->get('unique_id'))->whereNull('deleted_at')->first();
            if ($user_details == Null) {
                return response()->json([
                    'status' => 0,
                    //'message' => 'App User Not Found',
                    'message' => __('user_messages.5'),
                    'message_code' => 5,
                ]);
            } else {
                if ($user_details->status == 0) {
                    return response()->json([
                        'status' => 3,
                        //'message' => 'Your account is currently blocked, so not authorised to allow any activity!',
                        'message' => __('user_messages.3'),
                        'message_code' => 3,
                    ]);
                }
                $user_details->currency = $request->get('select_currency');
                $user_details->language = $request->get('select_language');
                $user_details->device_token = $request->get('device_token');
                $user_details->login_device = $request->get('login_device') != Null ? $request->get('login_device') : 0;
                $user_details->verified_at = date('Y-m-d H:i:s');
                $user_details->save();
                $user_details->generateAccessToken($user_details->id);
                return $this->userClassApi->userLoginRegisterUpdateDetails($user_details);
            }
        }

    }
}
