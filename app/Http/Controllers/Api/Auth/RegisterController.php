<?php

namespace App\Http\Controllers\Api\Auth;

use App\Classes\AdminClass;
use App\Classes\AuthAlertClass;
use App\Classes\NotificationClass;
use App\Classes\TokenClassApi;
use App\Classes\UserClassApi;
use App\Models\User;
use App\Models\UserReferHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\ColombianMobileNumber;
use App\Support\ColombiaFormValidation;
use Intervention\Image\Laravel\Facades\Image;

class RegisterController extends Controller
{

    private $notificationClass;

    private $userClassApi;
    private $tokenClassApi;
    private $adminClass;

    public function __construct(TokenClassApi $tokenClassApi, UserClassApi $userClassApi, AdminClass $adminClass, NotificationClass $notificationClass)
    {
        $this->notificationClass = $notificationClass;
        $this->userClassApi = $userClassApi;
        $this->tokenClassApi = $tokenClassApi;
        $this->adminClass = $adminClass;
    }

    //customer register
    public function postCustomerRegister(Request $request) {
        $check_authentication = (new AuthAlertClass())->checkAuthorizationApp($request);
        if ($check_authentication->getData()->status != 1){
            return $check_authentication;
        }

        $countryCode = ColombiaFormValidation::normalizeCountryDialCode($request->get('select_country_code'));
        $normalizedContact = ColombiaFormValidation::normalizeColombianMobile(
            $request->get('contact_number'),
            $countryCode
        );
        $request->merge([
            'contact_number' => $normalizedContact,
            'select_country_code' => $countryCode,
        ]);

        $registerUser = User::query()
            ->where('id', '=', $request->get('user_id'))
            ->whereNull('deleted_at')
            ->first();

        $loginType = (string) ($registerUser?->login_type ?? '');
        $isSocialSignup = in_array($loginType, ['facebook', 'google', 'apple'], true);
        $isPhoneSignup = ! $isSocialSignup;
        $isAppleSignup = $loginType === 'apple';

        $emergencyCountryCode = ColombiaFormValidation::normalizeCountryDialCode(
            $request->get('emergency_country_code') ?? $countryCode
        );
        $normalizedEmergency = $request->filled('emergency_contact')
            ? ColombiaFormValidation::normalizeColombianMobile($request->get('emergency_contact'), $emergencyCountryCode)
            : '';

        $nameRule = $isAppleSignup ? 'nullable|string|max:255' : 'required|string|max:255';

        $rules = [
            'user_id' => 'required|numeric',
            'access_token' => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            'profile_image' => 'nullable',
            'select_country_code' => 'required',
            'first_name' => $nameRule,
            'last_name' => $nameRule,
            'full_name' => 'nullable|string|max:255',
            'refer_code' => 'nullable',
            'emergency_contact' => ['nullable', 'numeric', new ColombianMobileNumber($emergencyCountryCode)],
            'emergency_contact_name' => 'nullable|string|max:120',
        ];

        if ($isPhoneSignup) {
            $rules['contact_number'] = ['required', 'numeric', new ColombianMobileNumber($countryCode)];
            $rules['email'] = [
                'nullable',
                'email',
                Rule::unique('users')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request->get('user_id'));
                    $query->where('email', '=', $request->get('email'));
                    $query->where('deleted_at', '=', null);
                }),
            ];
        } else {
            $rules['contact_number'] = ['nullable', 'numeric', new ColombianMobileNumber($countryCode)];
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request->get('user_id'));
                    $query->where('email', '=', $request->get('email'));
                    $query->where('deleted_at', '=', null);
                }),
            ];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['email']['Unique'])) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.11'),
                    "message_code" => 11,
                ]);
            }
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

        if ($normalizedContact !== '' && ColombiaFormValidation::contactTakenByOtherUser(
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

        $user_details = User::query()->where('id', '=', $request->get('user_id'))->where('access_token', '=', $request->get('access_token'))->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                //'message' => "User not found!",
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        if ($user_details->verified_at == Null){
            return response()->json([
                'status' => 2,
                //'message' => "User Not Verified!",
                'message' => __('user_messages.2'),
                'message_code' => 2,
            ]);
        }

        $firstName = trim((string) ($request->get('first_name') ?: $request->get('full_name') ?: ''));
        $lastName = trim((string) ($request->get('last_name') ?: ''));
        if ($firstName !== '') {
            $user_details->first_name = ucwords(strtolower($firstName));
        }
        if ($lastName !== '') {
            $user_details->last_name = ucwords(strtolower($lastName));
        }
        $email = trim((string) $request->get('email'));
        $user_details->email = $email !== '' ? $email : null;
        if ($normalizedContact !== '') {
            $user_details->contact_number = $normalizedContact;
            $user_details->country_code = $countryCode;
        }
        if ($normalizedEmergency !== '') {
            $user_details->emergency_contact = $normalizedEmergency;
            $user_details->emergency_country_code = $emergencyCountryCode;
        }
        if ($request->filled('emergency_contact_name')) {
            $emergencyName = trim((string) $request->get('emergency_contact_name'));
            $user_details->emergency_contact_name = $emergencyName !== '' ? $emergencyName : null;
        }
        $user_details->status = 1;
        $user_details->is_register = 1;
        $user_details->accepted_terms_at = now();
        $user_details->accepted_data_processing_at = now();
        $user_details->accepted_legal_version = (string) config('xisti.legal.consent_version', '2026-06-legal-v1');

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

        $user_details->save();

        $general_settings = request()->get("general_settings");
        if ($general_settings != Null) {
            if (trim($request->get('refer_code')) != Null) {
                $refer_code = strtoupper(trim($request->get('refer_code')));
                $find_refer = User::query()->where('invite_code', $refer_code)->first();
                if ($find_refer != Null) {
                    $used_user_discount = $general_settings->used_user_discount;
                    $used_user_discount_type = $general_settings->used_user_discount_type;
                    $refer_user_discount = $general_settings->refer_user_discount;
                    $refer_user_discount_type = $general_settings->refer_user_discount_type;
                    if ($used_user_discount != 0 || $refer_user_discount != 0) {
                        $refer_history = new UserReferHistory();
                        $refer_history->user_id = $user_details->id;
                        //$refer_history->user_id = 13;
                        $refer_history->refer_id = $find_refer->id;
                        $refer_history->user_discount = $used_user_discount;
                        $refer_history->user_discount_type = $used_user_discount_type;
                        $refer_history->refer_discount = $refer_user_discount;
                        $refer_history->refer_discount_type = $refer_user_discount_type;
                        $refer_history->user_status = 0;
                        $refer_history->refer_status = 0;
                        $refer_history->save();
                        $find_refer->pending_refer_discount = $find_refer->pending_refer_discount + 1;
                        $find_refer->save();
                        $user_details->pending_refer_discount = $user_details->pending_refer_discount + 1;
                        $user_details->save();
                    }
                }
            }
        }
        $user_details->InviteCode($user_details->id, $user_details->first_name);
        if ($general_settings !=  Null){
            if ($general_settings->send_mail == 1) {
                $notificationClass = new NotificationClass();
                try {
                    $mail_type = "customer_signup";
                    $to_mail = $user_details->email;
                    $subject = "Welcome to " . $general_settings->mail_site_name;
                    $disp_data = array("##user_name##" => $user_details->first_name );
                    $mail_return_data = $notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                } catch (\Exception $e) {}
                if ($general_settings->send_receive_email != Null ){
                    try {
                        $mail_type = "admin_new_user_signup";
                        $to_mail = $general_settings->send_receive_email;
                        $subject = "New User has Register";
                        $disp_data = array("##user_name##" => $user_details->first_name , "##mail_site_name##" => $general_settings->mail_site_name);
                        $mail_return_data = $notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                    } catch (\Exception $e) {}
                }
            }
        }
        return $this->userClassApi->userLoginRegisterUpdateDetails($user_details);
    }
}
