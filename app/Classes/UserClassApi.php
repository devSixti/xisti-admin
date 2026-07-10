<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13-12-2018
 * Time: 03:48 PM
 */

namespace App\Classes;


use App\Models\AdminAreaList;
use App\Models\GeneralSettings;
use App\Models\ProviderDocuments;
use App\Models\ProviderUserRunningService;
use App\Models\RequiredDocuments;
use App\Models\RestrictedArea;
use App\Models\ServiceSettings;
use App\Models\Sos;
use App\Models\TopUpWallet;
use App\Models\TransportCourierDetails;
use App\Models\TransportDriverDetails;
use App\Models\TransportRatings;
use App\Models\TransportRideBook;
use App\Helpers\ApiValidationHelper;
use App\Helpers\DeliveryVehicleHelper;
use App\Helpers\EncomiendaHelper;
use App\Helpers\RideKindHelper;
use App\Helpers\AppMobileSettingsHelper;
use App\Helpers\DestinationPaymentHelper;
use App\Helpers\RideLifecycleHelper;
use App\Rules\ColombianMobileNumber;
use App\Rules\ColombianNationalId;
use App\Support\ColombiaFormValidation;
use App\Support\VehicleDocumentRules;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\UserAddress;
use App\Models\UserCardDetails;
use App\Models\UserReferHistory;
use App\Models\UserRideWayPoint;
use App\Models\UserRunningRide;
use App\Models\UserWalletTransaction;
use App\Models\VehicleService;
use App\Models\WorldCurrency;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;


class UserClassApi
{
    private $notificationClass;
    private $adminClassApi;
    private $user_type = 0;
    private $driver_type = 2;


    public function __construct(NotificationClass $notificationClass , AdminClass $adminClassApi)
    {
        $this->notificationClass = $notificationClass;
        $this->adminClassApi = $adminClassApi;
    }

    //check User Allowed or Not
    public function checkUserAllow($user_id, $access_token){
        $user_details = User::query()->select('id', 'first_name', 'last_name', 'email', 'verified_at', 'contact_number', 'login_type', 'login_id', 'password', 'avatar', 'invite_code', 'access_token', 'device_token', 'status as user_status', 'currency', 'pending_refer_discount', 'language', 'is_register', 'area_id','is_driver_type','is_driver_status','driver_vehicle_status','driver_doc_status','driver_current_status','active_mode')->where('id',"=", $user_id)->whereNull('users.deleted_at')->first();
        if ($user_details != Null) {
            if ($user_details->user_status == 0) {
                return response()->json([
                    'status' => 3,
                    'message' => __('user_messages.3'),
                    'message_code' => 3,
                ]);
            }
            $storedToken = (string) ($user_details->access_token ?? '');
            $providedToken = (string) ($access_token ?? '');
            if ($storedToken === '' || ! hash_equals($storedToken, $providedToken)) {
                return response()->json([
                    'status' => 4,
                    'message' => __('user_messages.4'),
                    'message_code' => 4,
                ]);
            }
            if ($user_details->verified_at == Null) {
                return response()->json([
                    'status' => 2,
                    'message' => __('user_messages.2'),
                    'message_code' => 2,
                ]);
            }
            if ($user_details->user_status == 0) {
                return response()->json([
                    'status' => 3,
                    'message' => __('user_messages.3'),
                    'message_code' => 3,
                ]);
            }
            return $user_details;
        } else {
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
    }

    /**
     * checkUserAllow / checkDriverRegisterAllow return JsonResponse on failure, User on success.
     */
    public function authJsonResponse(mixed $check): ?\Illuminate\Http\JsonResponse
    {
        return $check instanceof \Illuminate\Http\JsonResponse ? $check : null;
    }

    //check Driver Allowed or Not
    public function checkDriverRegisterAllow($driver_id)
    {
        $driver_details = User::query()->where('id', $driver_id)->whereNull('users.deleted_at')->first();
        if ($driver_details != Null) {
                if ($driver_details->is_driver_status == 2) {
                    return response()->json([
                        "status" => 3,
                        "message" => __('driver_messages.3'),
                        "message_code" => 3
                    ]);
                }
                if ($driver_details->is_driver_status == 3) {
                    return response()->json([
                        "status" => 8,
                        "message" => __('driver_messages.8'),
                        "message_code" => 8
                    ]);
                }
                return $driver_details;
        } else {
            return response()->json([
                "status" => 5,
                "message" => __('driver_messages.5'),
                "message_code" => 5
            ]);
        }
    }

    // user Login & Register Update Details code
    public function userLoginRegisterUpdateDetails($user_details) {
        $cash_payment = 0;
        $card_payment = 0;
        $wallet_payment = 0;
        $general_settings = \App\Support\RequestSettingsHelper::generalSettings();
        if ($general_settings != Null){
            $cash_payment = $general_settings->cash_payment;
            $card_payment = $general_settings->card_payment;
            $wallet_payment = $general_settings->wallet_payment;
        }

        $hasPendingOtp = UserVerification::query()
            ->where('user_id', (int) $user_details['id'])
            ->exists();
        $userVerified = ($user_details['verified_at'] != null && ! $hasPendingOtp) ? 1 : 0;

        $is_register = $user_details['is_register'] - 0;
        $final_response_array = array_merge([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
            'is_register' => $is_register,
            'user_id' => $user_details['id'],
            'access_token' => (string) ($user_details['access_token'] ?? ''),
            'contact_number' => $user_details['contact_number'] != Null ? $user_details['contact_number'] : "",
            'select_country_code' => $user_details['country_code'] != Null ? $user_details['country_code'] : "",
            'login_type' => $user_details['login_type'],
            'user_verified' => $userVerified,
            'otp_delivery_channel' => ($userVerified === 1
                ? TokenClassApi::CHANNEL_SMS
                : TokenClassApi::lastChannelForUser((int) $user_details['id'])),
            "cash_payment" => $cash_payment - 0,
            "online_payment" => $card_payment - 0,
            "wallet_payment" => $wallet_payment - 0,
            "active_mode" => $user_details['active_mode'],
            'unique_id' => $user_details['unique_id'] != Null ? $user_details['unique_id'] : "",
        ], \App\Helpers\AppMobileSettingsHelper::pricingAndCommissionPayload($general_settings));
        if ($user_details['avatar'] != Null) {
            if (filter_var($user_details['avatar'], FILTER_VALIDATE_URL) == true) {
                $avatar = $user_details['avatar'];
            } else {
                $avatar = url('/assets/images/profile-images/customer/' . $user_details['avatar']);
            }
        } else {
            $avatar = Null;
        }
        if ($user_details['emergency_contact'] != Null && trim((string) $user_details['emergency_contact']) !== '') {
            $final_response_array['emergency_contact'] = $user_details['emergency_contact'] . '';
            $final_response_array['emergency_country_code'] = $user_details['emergency_country_code'] != Null
                ? $user_details['emergency_country_code']
                : $user_details['country_code'];
        }
        if (!empty($user_details['emergency_contact_name'])) {
            $final_response_array['emergency_contact_name'] = trim((string) $user_details['emergency_contact_name']);
        }
        if (in_array($user_details['login_type'], ['google', 'facebook', 'apple'], true)) {
            if (!empty($user_details['email'])) {
                $final_response_array['email'] = $user_details['email'];
            }
            if (!empty($user_details['first_name'])) {
                $final_response_array['user_name'] = $user_details['first_name'];
            }
            if ($avatar !== null) {
                $final_response_array['profile_image'] = $avatar;
            }
        }
        if ($is_register == 1) {
            if ($user_details instanceof User) {
                $user_details->ensureInviteCode();
                $referralCode = (string) $user_details->invite_code;
            } else {
                $referralCode = (string) ($user_details['invite_code'] ?? '');
            }
            $final_response_array['user_name'] = $user_details['first_name'].'';
            $final_response_array['email'] = $user_details['email'].'';
            $final_response_array['referral_code'] = $referralCode;
            $final_response_array['gender'] = $user_details['gender'];
            $final_response_array['profile_image'] = $avatar;
            $final_response_array['select_currency'] = $user_details['currency'].'';
            $final_response_array['select_language'] = $user_details['language'].'';
            $final_response_array['emergency_contact'] = $user_details['emergency_contact'].'';
            $final_response_array['emergency_country_code'] = $user_details['emergency_country_code'] != Null ? $user_details['emergency_country_code'] : $user_details['country_code'];
            if (!empty($user_details['emergency_contact_name'])) {
                $final_response_array['emergency_contact_name'] = trim((string) $user_details['emergency_contact_name']);
            }
            $final_response_array['server_time_zone'] = config('app.timezone');
            $final_response_array['is_driver_type'] = $user_details['is_driver_type'];
            $final_response_array['is_driver_status'] = $user_details['is_driver_status'];
            $final_response_array['driver_doc_status'] = $user_details['driver_doc_status'];
            $final_response_array['driver_vehicle_status'] = $user_details['driver_vehicle_status'];
        }
        if ($is_register === 1) {
            if ($user_details instanceof User) {
                $final_response_array['referral_code'] = $user_details->ensureInviteCode();
            } elseif (empty($final_response_array['referral_code'] ?? '')) {
                $final_response_array['referral_code'] = (string) ($user_details['invite_code'] ?? '');
            }
        }
        return response()->json($final_response_array);
    }

    //for Transport Ride Booking
    public function TransportRideBook($request, $user_details)
    {
        $recipientCountry = ColombiaFormValidation::normalizeCountryDialCode(
            $request->get('recipient_country_code', $request->get('select_country_code', '+57'))
        );
        $normalizedRecipient = ColombiaFormValidation::normalizeColombianMobile(
            $request->get('recipient_contact_number'),
            $recipientCountry
        );
        $otherCountry = ColombiaFormValidation::normalizeCountryDialCode(
            $request->get('other_user_country_code', $request->get('select_country_code', '+57'))
        );
        $normalizedOtherContact = ColombiaFormValidation::normalizeColombianMobile(
            $request->get('other_user_contact_number'),
            $otherCountry
        );
        $request->merge([
            'recipient_country_code' => $recipientCountry,
            'recipient_contact_number' => $normalizedRecipient,
            'other_user_country_code' => $otherCountry,
            'other_user_contact_number' => $normalizedOtherContact,
        ]);

        $request->merge(AppMobileSettingsHelper::normalizeOptionalCourierPackageFields($request->all()));

        $validator = Validator::make($request->all(), array_merge([
            "service_id"=>"required|numeric",
            "offered_fare" => "required",
            "estimated_time" => "required",
            "total_distance" => "required",
            "pickup_date_time" => "nullable",
            "address_list" => "required",
            "additional_remarks" => "nullable",
            "min_bargain_amt" => "required",
            "max_bargain_amt" => "required",
            "recipient_name" => "required_if:service_id,==,4|required_if:errand_type,encomienda|max:80",
            "recipient_contact_number" => [
                "required_if:service_id,==,4",
                "required_if:errand_type,encomienda",
                "numeric",
                new ColombianMobileNumber($recipientCountry),
            ],
            "item_description" => "required_if:service_id,==,4|required_if:errand_type,encomienda|max:500",
            "estimate_price" => "nullable|required_if:errand_type,encomienda|numeric|min:0",
            "document_number" => ["nullable", new ColombianNationalId()],
            "is_auto_accept" => "required|in:0,1",
            "ride_for_other" => "nullable|in:0,1",
            "other_user_name" => "required_if:ride_for_other,==,1|max:80",
            "other_user_contact_number" => [
                "required_if:ride_for_other,==,1",
                "numeric",
                new ColombianMobileNumber($otherCountry),
            ],
            "handicap" => "required",
            "child_seat" => "required",
            "payment_type" => "required",
            "destination_payment_method" => DestinationPaymentHelper::validationRule(),
            "requested_vehicle_service_id" => "nullable|integer|in:1,3,4",
            "delivery_variant" => "nullable|string|max:64",
            "errand_type" => "nullable|in:delivery,encomienda,acarreo",
            "acarreo_vehicle_variant" => "nullable|in:motocarguero,camion,jaula,motocarro",
            "estimated_service_date" => "nullable|date",
            "delivery_direction" => "nullable|in:send,receive",
            "sender_name" => "nullable|max:80",
            "sender_contact_number" => "nullable|numeric",
        ], AppMobileSettingsHelper::courierPackageDimensionValidationRules()));

        $errandType = EncomiendaHelper::normalizedErrandType(
            $request->get('errand_type'),
            (int) $request->get('service_id')
        );
        $isEncomiendaBooking = $errandType === EncomiendaHelper::ERRAND_ENCOMIENDA;
        $isAcarreoBooking = $errandType === EncomiendaHelper::ERRAND_ACARREO;
        if ($isEncomiendaBooking) {
            $validator->after(function ($v) use ($request) {
                if (!DeliveryVehicleHelper::isValidRequestedVehicleServiceId((int) $request->get('requested_vehicle_service_id'))) {
                    $v->errors()->add('requested_vehicle_service_id', __('user_messages.9'));
                }
                if (trim((string) $request->get('item_description')) === '') {
                    $v->errors()->add('item_description', __('user_messages.9'));
                }
                if (trim((string) $request->get('estimate_price')) === '' && (float) $request->get('estimate_price') <= 0) {
                    $v->errors()->add('estimate_price', __('user_messages.9'));
                }
            });
        }
        if ($isAcarreoBooking) {
            $validator->after(function ($v) use ($request) {
                if (trim((string) $request->get('item_description')) === '') {
                    $v->errors()->add('item_description', __('user_messages.9'));
                }
                if ((float) $request->get('offered_fare') <= 0) {
                    $v->errors()->add('offered_fare', __('user_messages.9'));
                }
                if (\App\Helpers\AcarreoHelper::normalizeVariant($request->get('acarreo_vehicle_variant')) === null) {
                    $v->errors()->add('acarreo_vehicle_variant', __('user_messages.9'));
                }
            });
        }
        if ($validator->fails()) {
            return ApiValidationHelper::jsonFromValidator($validator);
        }

            try {
                $address_list = json_decode($request->get('address_list'), true);
                $new_address_list = [];
                foreach ($address_list as $key => $address) {
                    if ($address['address'] == Null || $address['address_lat'] == Null || $address['address_long'] == Null) {
                        unset($address_list[$key]);
                    }
                    $new_address_list[] = $address;
                }
            }catch (\Exception $e){
                return response()->json([
                    "status" => 0,
                    'message' => __('user_messages.9'),
                    "message_code" => 9,
                ]);
            }

        $driver_algorithm = 0;
        $timeout = 60;
        $general_settings = request()->get("general_settings");
        $last_amount = $this->notificationClass->getWalletBalance($request->get('user_id'));
        if ($user_details['currency'] != Null) {
            $user_currency = WorldCurrency::query()->where('symbol', $user_details['currency'])->first();
            if ($user_currency == Null) {
                $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
            }
        } else {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency->ratio;
        $amount = round($request['offered_fare'] / $currency,2);

        if ($general_settings != Null) {
            $step = max(1, (int) ($general_settings->fare_negotiation_step ?? 500));
            if ($user_currency->symbol === 'COP') {
                $offered = (float) $request['offered_fare'];
                if (fmod($offered, $step) > 0.009) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('user_messages.388', ['step' => $step]),
                        "message_code" => 388,
                    ]);
                }
            }
        }

        if ($general_settings != Null){
            //check user_min_amount condition wallet
            if(( $request->payment_type == 3) && ($last_amount < 0 || $last_amount < $amount)) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('user_messages.333'),
                        "message_code" => 333,
                    ]);
            }
            $driver_algorithm = $general_settings->driver_algorithm - 0;
            $timeout = $general_settings->user_timeout != NUll ? round($general_settings->user_timeout) : 60;
        }

        $address_lat = trim($new_address_list[0]['address_lat']);
        $address_long = trim($new_address_list[0]['address_long']);
        $area_details = RestrictedArea::query()->where('status',1)->get();
        $latitudes = $longitudes = '';
        foreach ($area_details as $area){
            $latitudes = $latitudes . $area->latitude . ',';
            $longitudes = $longitudes . $area->longitude . ',';
        }

        $restricted_lat = explode(',',substr($latitudes, 0, -1));
        $restricted_long = explode(',',substr($longitudes, 0, -1));
        $points_polygon = count($restricted_lat);
        if($this->adminClassApi->is_in_restricted_area($points_polygon,$restricted_lat,$restricted_long,$address_lat,$address_long)){
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.194'),
                "message_code" => 194,
            ]);
        }
        $address_count = count($new_address_list);
        if ($address_count < 2) {
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }

        //find price service_type
        $get_vehicle_service = VehicleService::query()->where('id', $request['service_id'])->where('status',1)->first();
        if ($get_vehicle_service == Null) {
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }

        if ((int) $get_vehicle_service->id === 5
            || ((string) ($get_vehicle_service->service_mode ?? '') === 'transport'
                && ! DeliveryVehicleHelper::isPassengerActiveVehicleServiceId((int) $get_vehicle_service->id))) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ]);
        }

        $current_lat = $new_address_list[0]['address_lat'];
        $current_long = $new_address_list[0]['address_long'];
        $area_id = 0;
        if($current_lat != Null &&  $current_long != Null) {
            $get_admin_area_list = AdminAreaList::query()->where('status', 1)->get();
            if ($get_admin_area_list->isNotEmpty()) {
                foreach ($get_admin_area_list as $get_area) {
                    $vertices_x = explode(",", $get_area->latitude);
                    $vertices_y = explode(",", $get_area->longitude);

                    $area_points_polygon = count($vertices_x) - 1;
                    $longitude_x = $current_lat;
                    $latitude_y = $current_long;

                    if ($this->adminClassApi->is_in_restricted_area($area_points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)) {
                        $area_id = $get_area->id;
                        break;
                    }
                }
            }
        }

        if ($user_details['currency'] != Null) {
            $user_currency = WorldCurrency::query()->where('symbol', $user_details['currency'])->first();
            if ($user_currency == Null) {
                $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
            }
        } else {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }

        $currency = $user_currency->ratio;
        $amount = round($request['offered_fare'] / $currency,2);

        $ride = new TransportRideBook();
        $ride->user_id = $request['user_id'];
        $ride->area_id = $area_id;
        $ride->vehicle_service_id = $get_vehicle_service->id;
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_ride_booking', 'delivery_variant')) {
            $variant = \App\Helpers\XistiVehicleVariantHelper::normalize($request->get('delivery_variant'));
            $ride->delivery_variant = $variant !== '' ? $variant : null;
        }
        $ride->vehicle_cost_for_km = $get_vehicle_service->cost_for_km;
        $ride->ride_no = $ride->generateRideNo();
        $isCourierRide = $errandType !== null;
        $general_settings = request()->get('general_settings');
        $ride->otp = ($general_settings && (int) $general_settings->ride_otp === 1)
            ? $ride->generateOtp(4)
            : null;
        $ride->user_name = $user_details['first_name'];
        if ($request['pickup_date_time'] != Null) {
            $destination_datetime = date('Y-m-d H:i:s', strtotime('+'.$request['estimated_time'].' minute',strtotime(date('Y-m-d H:i:s', strtotime($request['pickup_date_time'])))));
            $ride->pickup_datetime = date('Y-m-d H:i:s', strtotime($request['pickup_date_time']));
            $ride->ride_type = 1;
        } else {
            $ride->pickup_datetime = date('Y-m-d H:i:s');
            $destination_datetime = date('Y-m-d H:i:s', strtotime('+'.$request['estimated_time'].' minute'));
        }

        $ride->destination_datetime = $destination_datetime;
        $ride->pickup_address = $new_address_list[0]['address'];
        $ride->pickup_lat = $new_address_list[0]['address_lat'];
        $ride->pickup_long = $new_address_list[0]['address_long'];
        $ride->destination_address = $new_address_list[$address_count - 1]['address'];
        $ride->destination_latlong = $new_address_list[$address_count - 1]['address_lat'] . ',' . $new_address_list[$address_count - 1]['address_long'];
        $ride->min_bargain_amt = $request['min_bargain_amt'] != Null ? $request['min_bargain_amt'] / $currency : 0 ;
        $ride->max_bargain_amt = $request['max_bargain_amt'] != Null ? $request['max_bargain_amt'] / $currency : 0 ;
        $ride->total_pay = $amount;
        $ride->offered_price = $amount;
        $ride->total_distance = $request['total_distance'];
        $ride->eta = $request['estimated_time'];
        $ride->payment_type = $request['payment_type'];
        $ride->destination_payment_method = DestinationPaymentHelper::normalize(
            $request->get('destination_payment_method', DestinationPaymentHelper::CASH)
        ) ?? DestinationPaymentHelper::CASH;
        $ride->payment_status = 0;
        $ride->handicap = $request['handicap'];
        $ride->child_seat = $request['child_seat'];
        $ride->driver_gender = $request->get('gender') - 0;
        $ride->tip = $request['tip'] != null ? round($request['tip'] / $currency) : 0;
        $ride->additional_request = ($request->get('additional_remarks') != "") ? $request->get('additional_remarks') : "";
        $ride->status = 0;
        $ride->driver_algorithm = $driver_algorithm;
        $date = new \DateTime('now', new \DateTimeZone(config('app.timezone')));
        $ride->retry_time = $date->format('Y-m-d H:i:s');
        $ride->ride_time_out = RideLifecycleHelper::rideTimeoutFromNow();
        $ride->is_auto_accept = $request['is_auto_accept'];

        if (Schema::hasColumn('user_ride_booking', 'delivery_direction')) {
            $ride->delivery_direction = $request->get('delivery_direction');
        }

        if($request['ride_for_other'] == 1){
            $ride->ride_for_other = $request['ride_for_other'];
            $ride->other_user_name = $request['other_user_name'];
            $ride->other_user_contact_number = $request['other_user_contact_number'];
        }
        $ride->save();

        if (EncomiendaHelper::shouldPersistCourierRow((int) $get_vehicle_service->id, $errandType)
            || \App\Helpers\AcarreoHelper::shouldPersistCourierRow($errandType)) {
            $courier_details = new TransportCourierDetails();
            $courier_details->ride_id = $ride->id;
            if (Schema::hasColumn('user_courier_service_details', 'errand_type')) {
                $courier_details->errand_type = $errandType ?? EncomiendaHelper::ERRAND_DELIVERY;
            }
            if (Schema::hasColumn('user_courier_service_details', 'acarreo_vehicle_variant') && $isAcarreoBooking) {
                $courier_details->acarreo_vehicle_variant = \App\Helpers\AcarreoHelper::normalizeVariant(
                    $request->get('acarreo_vehicle_variant')
                );
            }
            if (Schema::hasColumn('user_courier_service_details', 'estimated_service_date') && $isAcarreoBooking) {
                $courier_details->estimated_service_date = $request->get('estimated_service_date');
            }
            $courier_details->recipient_name = $request['recipient_name'] ?? '';
            $courier_details->recipient_contact_number = ColombiaFormValidation::normalizeColombianMobile(
                $request['recipient_contact_number'] ?? '',
                $request->get('recipient_country_code', '+57')
            );
            if (Schema::hasColumn('user_courier_service_details', 'delivery_direction')) {
                $courier_details->delivery_direction = $request->get('delivery_direction');
            }
            if (Schema::hasColumn('user_courier_service_details', 'sender_name')) {
                $courier_details->sender_name = $request->get('sender_name', '');
                $courier_details->sender_contact_number = ColombiaFormValidation::normalizeColombianMobile(
                    $request->get('sender_contact_number', ''),
                    $request->get('recipient_country_code', '+57')
                );
            }
            $courier_details->item_description = $request['item_description'] ?? '';
            $courier_details->estimate_price = round((float) ($request['estimate_price'] ?? 0), 2);
            if (!$isEncomiendaBooking && !$isAcarreoBooking) {
                AppMobileSettingsHelper::applyCourierPackageMetricsToModel($courier_details, $request);
            }
            $requestedVehicleServiceId = (int) $request->get('requested_vehicle_service_id');
            if (\App\Helpers\DeliveryVehicleHelper::isValidRequestedVehicleServiceId($requestedVehicleServiceId)) {
                $courier_details->requested_vehicle_service_id = $requestedVehicleServiceId;
            } elseif ($isEncomiendaBooking && in_array((int) $get_vehicle_service->id, DeliveryVehicleHelper::PASSENGER_DELIVERY_SERVICE_IDS, true)) {
                $courier_details->requested_vehicle_service_id = (int) $get_vehicle_service->id;
            }
            $courier_details->save();
        }

        if ($request['service_id'] != 4 && $address_count > 2) {
            $ride->is_way_point = 1;
            $ride->way_point_status = 0;
            $ride->save();

            $ride_way_point = new UserRideWayPoint();
            $ride_way_point->ride_id = $ride->id;
            $ride_way_point->way_point_1 = $new_address_list[1]['address'];
            $ride_way_point->lat_long_1 = $new_address_list[1]['address_lat'] . ',' . $new_address_list[1]['address_long'];
            if ($address_count > 3) {
                $ride_way_point->way_point_2 = $new_address_list[2]['address'];
                $ride_way_point->lat_long_2 = $new_address_list[2]['address_lat'] . ',' . $new_address_list[2]['address_long'];
            }
            if ($address_count > 4) {
                $ride_way_point->way_point_3 = $new_address_list[3]['address'];
                $ride_way_point->lat_long_3 = $new_address_list[3]['address_lat'] . ',' . $new_address_list[3]['address_long'];
            }
            $ride_way_point->save();
        } else {
            $ride->is_way_point = 0;
            $ride->way_point_status = 0;
            $ride->save();
        }

        $user_running_ride = UserRunningRide::query()->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->first();
        if ($user_running_ride == Null) {
            $user_running_ride = new UserRunningRide();
            $user_running_ride->user_id = $ride->user_id;
            $user_running_ride->booking_id = $ride->id;
            $user_running_ride->save();
        }

        $service_settings = ServiceSettings::query()->first();
        if ($service_settings != Null) {
            $admin_commission = $service_settings->admin_commission;
        } else {
            $admin_commission = 0;
        }
        $this->notificationClass->RequestAllDrivers($ride->id,$ride->pickup_lat,$ride->pickup_long,$get_vehicle_service->id,$ride->pickup_address,$ride->destination_address,$ride->offered_price,$ride->handicap,$ride->child_seat);

        return response()->json([
            "status" => 1,
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "ride_id" => $ride->id,
            "booking_no" => $ride->ride_no,
            "pickup_date_time" => Date('d M, Y', strtotime($ride->pickup_datetime)),
            "pickup_address" => $ride->pickup_address,
            "destination_address" => $ride->destination_address,
            "ride_status" => $ride->status,
            "accept_time_out" => $timeout,
        ]);
    }

    //for Transport Ride Cancel Code
    public function transportRideBookingCancel($ride_id, $cancel_by, $cancel_reason)
    {
        $ride = TransportRideBook::query()->where('id', $ride_id)->first();
        if ($ride != Null) {
            $ride->cancel_by = $cancel_by;
            if ($cancel_reason != Null) {
                $ride->cancel_reason = $cancel_reason;
            }
            $ride_status = $ride->status;
            $ride->otp = null;
            $ride->status = 4;
            $ride->save();

            try {
                (new FirebaseService())->deleteOrderChat($ride->ride_no, $ride->id);
            } catch (\Throwable $e) {
                \Log::warning('transportRideBookingCancel: firebase chat delete failed', [
                    'ride_id' => $ride_id,
                    'error' => $e->getMessage(),
                ]);
            }

            //refer history code
            if($ride_status >= 0 && $ride_status <= 9 ){
                if ($ride->refer_discount > 0) {
                    $user = User::query()->select('id','pending_refer_discount')->where('id', $ride->user_id)->whereNull('deleted_at')->first();
                    if ($user != Null) {
                        $user_refer_history = UserReferHistory::query()->where('id',$ride->user_refer_history_id)->where('user_id', $ride->user_id)->where('user_status', 1)->first();
                        if ($user_refer_history != Null) {
                            $user_refer_history->user_status = 0;
                            $user_refer_history->save();
                            $user->pending_refer_discount = $user->pending_refer_discount + 1;
                            $user->save();
                        } else {
                            $user_refer_history = UserReferHistory::query()->where('id',$ride->user_refer_history_id)->where('refer_id', $ride->user_id)->where('refer_status', 1)->first();
                            if ($user_refer_history != Null) {
                                $user_refer_history->refer_status = 0;
                                $user_refer_history->save();
                                $user->pending_refer_discount = $user->pending_refer_discount + 1;
                                $user->save();
                            }
                        }
                    }
                }
            }
            if ($ride->driver_id != Null) {
                $driver_details = TransportDriverDetails::query()
                    ->select('transport_driver_details.*','users.language','users.login_device','users.device_token')
                    ->join('users','users.id','=','transport_driver_details.user_id')
                    ->where('transport_driver_details.user_id',$ride->driver_id)->first();


                if ($driver_details != Null) {
                    $this->notificationClass->driverCancelNotification($ride->id,$driver_details->device_token,$driver_details->user_id, $driver_details->login_device,$driver_details->language,null);
                }
            }
            try {
                UserRunningRide::query()->where('user_id', $ride->user_id)->where('booking_id', $ride_id)->delete();
                ProviderUserRunningService::query()->where('user_id', $ride->user_id)->where('booking_id', $ride_id)->delete();
            }catch (\Exception $e){}
            return response()->json([
                "status" => 1,
//                "message" => "Success!",
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "ride_id" => $ride->id,
            ]);
        } else {
            return response()->json([
                'status' => 0,
//                'message' => "Rides Not Found!",
                'message' => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    public function findTransportAssignDriverDetails($driver_id, $status)
    {
        if ($status == true) {
            if ($driver_id == Null) {
                return [
                    "status" => 0,
                    "message" => __('user_messages.15'),
                    "message_code" => 15,
                ];
            }
        }
        $driver_details = TransportDriverDetails::query()->select(DB::raw("CONCAT(users.first_name,'') as driver_name"), 'users.avatar', 'users.contact_number','users.country_code',  'users.rating',
            'transport_vehicle_type.name as vehicle_type_name', 'transport_vehicle_type.icon_name as vehicle_type_icon','transport_driver_details.vehicle_image')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
            ->where('transport_driver_details.user_id', $driver_id)
            ->whereNull('users.deleted_at')
            ->first();
        if ($driver_details != Null) {
            if ($driver_details->avatar != Null) {
                $image_path = asset('assets/images/profile-images/customer/' . $driver_details->avatar);
            } else {
                $image_path = "";
            }
            if ($driver_details->vehicle_type_icon != Null) {
                $vehicle_type_icon = asset('assets/images/service-category/transport-service-type/' . $driver_details->vehicle_type_icon);
            } else {
                $vehicle_type_icon = "";
            }
            return [
                "status" => 1,
//                "message" => "success!",
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "driver_name" => $driver_details->driver_name,
                "driver_image" => $image_path,
                "driver_rating" => $driver_details->rating,
                "driver_contact_number" => $driver_details->country_code."".$driver_details->contact_number,

            ];
        } else {
            return [
                "status" => 1,
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "driver_name" => '',
                "driver_image" => '',
                "driver_rating" => 0,
                "driver_selected_service_cat_id" => 0,
                "service_type" => '',
                "service_type_icon" => '',
                "driver_contact_number" => '',
            ];
        }
    }

    //rider details for transport assign driver details (TAD),transport completed invoice (TCI), transport receipt details(TRD)
    public function findRideDetailsforTADorTCIorTRD($ride_id, $forTAD, $forTCI, $forTRD)
    {
        $ride = TransportRideBook::query()->select('user_ride_booking.*','transport_vehicle_type.name as vehicle_type_name', 'transport_vehicle_type.icon_name as vehicle_type_icon','transport_vehicle_type.service_id')
            ->leftJoin('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id')
            ->where('user_ride_booking.id', $ride_id)
            ->first();
        $settings = request()->get('general_settings');
        if ($ride != Null) {

            $user_lang = User::query()->where('id', $ride->user_id)->select('language')->first();
            $user_language = (isset($user_lang['language']) && $user_lang['language'] != null && $user_lang['language'] != '')
                ? $user_lang->language
                : 'en';
            $user_lang = $user_language != "en" ? $user_language . "_" : "";

            $destination_coordinates = explode(',', $ride->destination_latlong);

            $km = " ".config('global.lang_constant.KM.'.$user_lang.'value');
            $min = " ".config('global.lang_constant.MIN.'.$user_lang.'value');
            $address_list=array();
            $waypoint_msg_code = 0;
            $waypoint_message = "";

            $address_list[] = [
                "address" => $ride->pickup_address,
                "address_lat" => trim($ride->pickup_lat),
                "address_long" => trim($ride->pickup_long)
            ];
            if ($ride->is_way_point == 1) {

                if($ride->way_point_status == 1){
                    $waypoint_msg_code = 272;
                    $waypoint_message = __('user_messages.272', [], $user_language);
                }elseif($ride->way_point_status == 2){
                    $waypoint_msg_code = 273;
                    $waypoint_message = __('user_messages.273', [], $user_language);
                }elseif($ride->way_point_status == 3){
                    $waypoint_msg_code = 274;
                    $waypoint_message = __('user_messages.274', [], $user_language);
                }else{
                    $waypoint_msg_code = 0;
                    $waypoint_message = "";
                }
                $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride->id)->first();
                if ($ride_way_point != Null) {
                    if ($ride_way_point->way_point_1 != Null && $ride_way_point->lat_long_1 != Null) {
                        $lat_long_1 = array_map('trim', explode(",", $ride_way_point->lat_long_1));
                        $address_list[] = [
                            "address" => $ride_way_point->way_point_1,
                            "address_lat" => trim($lat_long_1[0]),
                            "address_long" => trim($lat_long_1[1])
                        ];
                    }
                    if ($ride_way_point->way_point_2 != Null && $ride_way_point->lat_long_2 != Null) {
                        $lat_long_2 = explode(",", $ride_way_point->lat_long_2);
                        $address_list[] = [
                            "address" => $ride_way_point->way_point_2,
                            "address_lat" => trim($lat_long_2[0]),
                            "address_long" => trim($lat_long_2[1])
                        ];
                    }
                    if ($ride_way_point->way_point_3 != Null && $ride_way_point->lat_long_3 != Null) {
                        $lat_long_3 = explode(",", $ride_way_point->lat_long_3);
                        $address_list[] = [
                            "address" => $ride_way_point->way_point_3,
                            "address_lat" => trim($lat_long_3[0]),
                            "address_long" => trim($lat_long_3[1])
                        ];
                    }
                }
            }
            $address_list[] = [
                "address" => $ride->destination_address,
                "address_lat" => trim($destination_coordinates[0] ?? Null),
                "address_long" => trim($destination_coordinates[1] ?? Null)
            ];

            if ($ride->vehicle_type_icon != Null) {
                $vehicle_type_icon = asset('assets/images/service-category/transport-service-type/' . $ride->vehicle_type_icon);
            } else {
                $vehicle_type_icon = "";
            }

            $courier_details = TransportCourierDetails::query()->where('ride_id', $ride->id)->first();
            if ($courier_details != Null) {
                $recipient_name = $courier_details->recipient_name;
                $recipient_contact_number = $courier_details->recipient_contact_number;
                $item_description = $courier_details->item_description;
                $estimate_price = $courier_details->estimate_price;
            }

            $general_settings = request()->get("general_settings");
            // invoice link
            $invoice_download_link = "";
            $order_details =  TransportRideBook::query()->select('id','user_id')->where('user_id',$ride->user_id)
                ->where('id','=',$ride->id)
                ->whereIn('status',[4,9,10])
                ->first();
            if($order_details != Null){
                $invoice_download_link = route('get:ride-invoice-download',[$order_details->id,"user",$order_details->user_id]);
            }

            $rideUser = User::query()
                ->select('id', 'first_name', 'emergency_contact', 'emergency_country_code', 'country_code')
                ->where('id', '=', $ride->user_id)
                ->first();
            $sos = \App\Support\SosContactListHelper::forUser($rideUser, $user_lang);

            $ride_details = [
                "status" => 1,
//                "message" => "Success",
                "message" => __('user_messages.1', [], $user_language),
                "message_code" => 1,
                "ride_id" => $ride->id,
                'additional_remark' => "" . $ride->additional_request,
                "ride_type" => $ride->ride_type - 0,
                "booking_no" => $ride->ride_no . "",
                "pickup_date_time" => $ride->pickup_datetime,
                'service_date_time' => date('Y-m-d H:i:s',strtotime($ride->created_at)),
                "ride_status" => $ride->status,
                "cancel_by" => $ride->cancel_by != Null ? $ride->cancel_by : "",
                "cancel_reason" => $ride->cancel_reason != Null ? $ride->cancel_reason : "",
                "service_type" => $ride->vehicle_type_name,
                "service_type_icon" => $vehicle_type_icon,
                'user_rating_status' => $ride->user_rating_status,
                'service_id'=>$ride->vehicle_service_id,
                'is_delivery' => RideKindHelper::isDeliveryFlag([
                    'service_id' => $ride->vehicle_service_id,
                    'item_description' => $item_description ?? '',
                    'recipient_name' => $recipient_name ?? '',
                ]),
                'otp' => $ride->otp.'',
                'is_otp' => $settings->ride_otp,
                'recipient_name' => isset($recipient_name) ? $recipient_name : '',
                'recipient_contact_number' => isset($recipient_contact_number) ? $recipient_contact_number : '',
                'item_description' => isset($item_description) ? $item_description : '',
                'estimate_price' => isset($estimate_price) ? $estimate_price : 0,
                "cash_payment" => $general_settings->cash_payment != NUll ? $general_settings->cash_payment: 0,
                "online_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
                "wallet_payment" => $general_settings->wallet_payment != NUll ? $general_settings->wallet_payment: 0,
                "invoice_download_link" => $invoice_download_link,
                "waypoint_msg_code" => $waypoint_msg_code,
                "waypoint_message" => $waypoint_message,
                "address_list" => $address_list,
                "ride_for_other" => $ride->ride_for_other,
                "other_user_name" => $ride->other_user_name,
                "other_user_contact_number" => $ride->other_user_contact_number,
                "sos_contact_list" => $sos,
                "order_chat_number" => (new FirebaseService())->CreateOrderNumberForChat($ride->ride_no,$ride->id) ,//for fire base chat
                "destination_payment_method" => $ride->destination_payment_method ?? '',
                "destination_payment_label" => DestinationPaymentHelper::label($ride->destination_payment_method ?? null, $user_language),
            ];

            if ($forTAD == true) {
                $driver_details = $this->findTransportAssignDriverDetails($ride->driver_id, true);
                if ($driver_details['status'] != 1) {
                    return response()->json($driver_details);
                }
                return response()->json(array_merge($ride_details, $driver_details));
            } elseif ($forTCI == true || $forTRD == true) {
                $driver_details = TransportDriverDetails::query()->select(
                    'transport_driver_details.vehicle_image',
                    'transport_driver_details.vehicle_company',
                    'transport_driver_details.plat_no',
                    'transport_driver_details.model_year',
                    'transport_driver_details.model_name',
                    'transport_driver_details.vehicle_color',
                    'users.id as driver_id',
                    'users.first_name as driver_name',
                    'transport_vehicle_type.name as vehicle_type_name',
                    'transport_driver_details.rating as driver_rating',
                    'users.avatar as driver_profile',
                    DB::raw("(concat(users.country_code,users.contact_number)) as driver_contact_number")
                )
                    ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
                    ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
                    ->where('transport_driver_details.user_id', $ride->driver_id)
//                    ->whereNull('users.deleted_at')
                    ->first();

                $total_ratings = TransportRatings::query()->where('driver_id',$ride->driver_id)->where('status',1)->count();

                if ($driver_details != Null) {
                    $driver_id = $driver_details->driver_id;
                    $driver_name = $driver_details->driver_name;
                    $driver_rating = $driver_details->driver_rating;
                    $contact_number = $driver_details->driver_contact_number;
                    $driver_profile = $driver_details->driver_profile != Null ? url('/assets/images/profile-images/customer/'.$driver_details->driver_profile) : '';
                    $driver_fcm_token = $driver_details->driver_fcm_token != Null ? $driver_details->driver_fcm_token : '';
                    $vehicle_company = $driver_details->vehicle_company != Null ? $driver_details->vehicle_company : '';
                    $plat_no = $driver_details->plat_no != Null ? $driver_details->plat_no : '';
                    $model_year = $driver_details->model_year != Null ? $driver_details->model_year : '';
                    $model_name = $driver_details->model_name != Null ? $driver_details->model_name : '';
                    $vehicle_color = $driver_details->vehicle_color != Null ? $driver_details->vehicle_color : '';
                    $vehicle_type_name = $driver_details->vehicle_type_name != Null ? $driver_details->vehicle_type_name : '';
                    $vehicle_image = $driver_details->vehicle_image != Null ? url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image) : '';
                } else {

                    //$time_fare = 0;
                    $service = '';
                    $driver_id = 0;
                    $driver_fcm_token = '';
                    $vehicle_company = '';
                    $plat_no = '';
                    $model_year = '';
                    $model_name = '';
                    $vehicle_color = '';
                    $driver_name = $driver_rating = $driver_profile = $contact_number = $vehicle_type_name = $vehicle_image = '';
                }

                $user_details = User::query()->where('id', $ride->user_id)->whereNull('deleted_at')->first();
                if ($user_details != Null) {
                    $user_currency = WorldCurrency::query()->where('symbol', $user_details->currency)->first();
                    if ($user_currency == Null) {
                        $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
                    }
                } else {
                    $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
                }

                $currency = $user_currency->ratio;

                $other_details = [
                    "driver_id" => $driver_id,
                    "driver_name" => $driver_name,
                    "driver_rating" => $driver_rating,
                    "driver_profile" => $driver_profile,
                    "contact_number" => $contact_number,
                    "driver_fcm_token" => $driver_fcm_token,
                    "total_ratings" => $total_ratings,
                    "vehicle_type_name" => $vehicle_type_name,
                    "vehicle_manufacture_name" => $vehicle_company,
                    "vehicle_plat_no" => $plat_no,
                    "vehicle_model_year" => intval($model_year),
                    "vehicle_model_name" => $model_name,
                    "vehicle_color" => $vehicle_color,
                    "vehicle_image" => $vehicle_image,
//                    "cost_per_km" => round( $ride->vehicle_cost_for_km * $currency, 2),
                    "total_distance" => $ride->total_distance."".$km,
                    "estimated_time" => $ride->eta ."".$min,
                    "user_refund_status" => $ride->user_refund_status,
                    "refund_amount" => $ride->refund_amount,
                    "refer_discount" => round($ride->refer_discount * $currency, 2),
                    "total_pay" => round($ride->total_pay * $currency, 2),
                    "toll_charge" => round($ride->toll_charge * $currency, 2),
                    "ride_fare" => round($ride->offered_price * $currency, 2),
                ];
                $invoiceBreakdown = \App\Helpers\RideInvoiceHelper::breakdownForCurrency(
                    (float) $ride->offered_price,
                    (float) $currency,
                    null,
                    (int) $ride->vehicle_service_id,
                    $ride->delivery_variant ?? null
                );
                $other_details = array_merge($other_details, [
                    'commission_percent' => $invoiceBreakdown['commission_percent'],
                    'commission_amount' => $invoiceBreakdown['commission_amount'],
                    'vat_rate_on_commission' => $invoiceBreakdown['vat_rate_on_commission'],
                    'vat_on_commission' => $invoiceBreakdown['vat_on_commission'],
                    'total_deduction' => $invoiceBreakdown['total_deduction'],
                    'net_driver_pay' => $invoiceBreakdown['net_driver_pay'],
                    'trip_value' => $invoiceBreakdown['trip_value'],
                ]);
                if ($forTRD == true) {
                    $add_other_details = [
                        "payment" => $ride->payment_type,
                        "payment_status" => $ride->payment_status,
                    ];
//                    $driver_details = $this->findTransportAssignDriverDetails($ride->driver_id, false);
//                    if ($driver_details['status'] != 1) {
//                        return response()->json($driver_details);
//                    }
                    $other_details = array_merge(array_merge($other_details, $add_other_details));
//                    $other_details = array_merge(array_merge($other_details, $driver_details));
                }
                return response()->json(array_merge(array_merge($ride_details, $other_details)));
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.9'),
                    "message_code" => 9,
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    //user address
    public function userAddressManage($request, $add, $update, $delete)
    {
        if ($add == true || $update == true) {
            $validator = Validator::make($request->all(), [
                "address" => "required",
                "type" => "nullable|in:1,2,3",
                "lat" => "required",
                "long" => "required",
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "status" => 0,
                    "message" => $validator->errors()->first(),
                    "message_code" => 9,
                ]);
            }
        }
        if ($update == true || $delete == true) {
            $validator = Validator::make($request->all(), [
                "address_id" => "required",
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "status" => 0,
                    "message" => $validator->errors()->first(),
                    "message_code" => 9,
                ]);
            }
        }
        $count_active_address = UserAddress::where('user_id', $request['user_id'])->where('status', 1)->count();
        $count_inactive_address = UserAddress::where('user_id', $request['user_id'])->where('status', 0)->count();
        if ($add == true) {
            if (($count_active_address + $count_inactive_address) < 5) {
                $address = new UserAddress();
                $address->user_id = $request['user_id'];
                if ($request['type'] != Null) {
                    $address->address_type = $request['type'];
                }
                $address->address = $request['address'];
                $address->lat_long = $request['lat'] . ',' . $request['long'];
                $address->save();
                return response()->json([
                    "status" => 1,
//                    "message" => "success!",
                    'message' => __('user_messages.1'),
                    "message_code" => 1,
                    "address_id" => $address->id
                ]);
            } else {
                if ($count_active_address == 5) {
                    return response()->json([
                        "status" => 0,
//                        "message" => "user only add five address,if you add new address then delete old address!",
                        'message' => __('user_messages.126'),
                        "message_code" => 9,
                    ]);
                } elseif ($count_inactive_address > 0) {
                    $address = UserAddress::where('user_id', $request['user_id'])->where('status', 0)->first();
                    $address->user_id = $request['user_id'];
                    $address->address_type = $request['type'];
                    $address->address = $request['address'];
                    $address->lat_long = $request['lat'] . ',' . $request['long'];
                    $address->status = 1;
                    $address->save();
                    return response()->json([
                        "status" => 1,
//                        "message" => "success!",
                        'message' => __('user_messages.1'),
                        "message_code" => 1,
                        "address_id" => $address->id
                    ]);
                } else {
                    return response()->json([
                        "status" => 0,
//                        "message" => "something went to wrong!",
                        'message' => __('user_messages.9'),
                        "message_code" => 9,
                    ]);
                }
            }
        } elseif ($update == true) {
            $address = UserAddress::where('user_id', $request['user_id'])->where('id', $request['address_id'])->first();
            $address->user_id = $request['user_id'];
            $address->address_type = $request['type'];
            $address->address = $request['address'];
            $address->lat_long = $request['lat'] . ',' . $request['long'];
            $address->save();
            return response()->json([
                "status" => 1,
//                "message" => "success!",
                'message' => __('user_messages.1'),
                "message_code" => 1,
                "address_id" => $address->id
            ]);
        } elseif ($delete == true) {
            $address = UserAddress::where('user_id', $request['user_id'])->where('id', $request['address_id'])->first();
            $address->status = 0;
            $address->save();
            return response()->json([
                "status" => 1,
//                "message" => "success!",
                'message' => __('user_messages.1'),
                "message_code" => 1,
            ]);
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "something went to wrong!",
                'message' => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }
    }


    function parse_signed_request($signed_request,$user_type) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        if($user_type == 'user'){
            $secret = "9668cf0f6a0c1cf724cf8c39cbb7606b";
        } elseif($user_type == 'driver'){
            $secret = "dd8fb54508a1e5aea5626adf57a03f94";
        } else{
            $secret = "d2aa6220e00d10cf139a030c1a51554d";
        }

        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    //Manage Card List Code (0:user,2:driver)
    public function manageCardList($card_provider_type, $card_user_id){
        $card_details = UserCardDetails::query()->select('card_details.id as card_id', 'card_details.holder_name as card_holder_name', 'card_details.card_number as card_number')
            ->where('user_id', "=", $card_user_id)
            ->where('card_provider_type', "=", $card_provider_type)
            ->get();
        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "card_list" => $card_details,
        ]);
    }

    //Add Card Manage Code (0:user,2:driver)
    public function addCardManage($card_provider_type, $card_user_id, $request_details){
        $general_settings = GeneralSettings::query()->first();
        if ($general_settings == null || (int)$general_settings->card_payment !== 1) {
            return response()->json([
                "status" => 0,
                "message" => "Card payment is disabled.",
                "message_code" => 9,
            ]);
        }

        $user = User::query()->select('id', 'email')->where('id', $card_user_id)->first();
        if ($user == null || empty($user->email)) {
            return response()->json([
                "status" => 0,
                "message" => "User email not found.",
                "message_code" => 9,
            ]);
        }

        $source_result = $this->wompiCreatePaymentSource($general_settings, $user->email, $request_details);
        if ((int)$source_result['status'] !== 1) {
            return response()->json($source_result);
        }

        $last_four = substr((string)$request_details['card_number'], -4);
        $card_details = new UserCardDetails();
        $card_details->user_id = $card_user_id;
        $card_details->card_provider_type = $card_provider_type;
        $card_details->holder_name = $request_details['holder_name'];
        $card_details->card_number = '**** **** **** ' . $last_four;
        $card_details->month = $request_details['month'];
        $card_details->year = $request_details['year'];
        $card_details->cvv = $request_details['cvv'];
        $card_details->wompi_payment_source_id = $source_result['payment_source_id'];
        $card_details->wompi_card_brand = $source_result['brand'];
        $card_details->wompi_card_last_four = $last_four;
        $card_details->save();

        $month = (($card_details->month == Null) ? Null: round($card_details->month));
        $year = (($card_details->year == Null) ? Null : round($card_details->year));
        $cvv = (($card_details->cvv == Null) ? Null : round($card_details->cvv));
        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "holder_name" => $card_details->holder_name,
            "card_number" => $card_details->card_number,
            "month" => $month,
            "year" => $year,
            "cvv" => $cvv,
            "wompi_payment_source_id" => $card_details->wompi_payment_source_id,
        ]);
    }

    //Delete Card Manage Code (0:user,2:driver)
    public function deleteCardManage($card_provider_type, $card_user_id, $card_id){
        UserCardDetails::query()
            ->where('card_provider_type', "=", $card_provider_type)
            ->where('user_id', "=", $card_user_id)
            ->where('id', "=" , $card_id)
            ->delete();
        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
        ]);
    }

    //Add Wallet Balance Code (0:user,2:driver)
    public function addWalletBalance($provider_type, $provider_id, $wallet_holder_name, $currency, $request_details){
        $general_settings = GeneralSettings::query()->first();
        if ($general_settings == null) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ]);
        }
        $topupAmount = (float) ($request_details['amount'] ?? 0);
        $minWompiTopup = (float) ($general_settings->driver_min_amount ?? 13000);
        if ($topupAmount > 0 && $topupAmount < $minWompiTopup) {
            $user_currency = WorldCurrency::query()->where('symbol', $currency)->first();
            if ($user_currency == null) {
                $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
            }
            $ratio = $user_currency != null ? $user_currency->ratio : 1;

            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.339', [
                    'amount' => round($minWompiTopup * $ratio, 2),
                    'currency_code' => $currency,
                ]),
                'message_code' => 339,
            ]);
        }
        $success_url = \Illuminate\Support\Facades\Route::has('payment.success') ? route('payment.success') : "";
        $failed_url = \Illuminate\Support\Facades\Route::has('payment.failed') ? route('payment.failed') : "";
        $payment_status = "APPROVED";
        $payment_transaction_id = "";
        $redirect_url = "";
        $is_card_payment = (
            isset($request_details['payment_method_type']) &&
            (int)$request_details['payment_method_type'] === 1
        );
        if ($is_card_payment) {
            if ($general_settings == null || (int)$general_settings->card_payment !== 1) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.377'),
                    "message_code" => 9,
                ]);
            }
            $wompi_charge = $this->wompiCreateCheckoutUrl(
                $general_settings,
                $wallet_holder_name,
                $request_details['amount'],
                $provider_id,
                $provider_type
            );
            $payment_status = (string)($wompi_charge['payment_status'] ?? 'FAILED');
            $payment_transaction_id = (string)($wompi_charge['payment_transaction_id'] ?? '');
            $redirect_url = (string)($wompi_charge['redirect_url'] ?? '');
            if ((int)$wompi_charge['status'] !== 1) {
                return response()->json($wompi_charge);
            }

            return response()->json([
                "status" => 1,
                "message" => "Checkout URL generated",
                "message_code" => 1,
                "wallet_balance" => round($this->notificationClass->getWalletBalance($provider_id) * 1, 2),
                "payment_status" => $payment_status,
                "payment_transaction_id" => $payment_transaction_id,
                "redirect_url" => $redirect_url,
                "success_url" => $success_url,
                "failed_url" => $failed_url,
                "error_url" => ""
            ]);
        }

        $provider_currency = WorldCurrency::query()->where('symbol', $currency)->first();
        if ($provider_currency == Null) {
            $provider_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $provider_currency != Null ? $provider_currency->ratio : 1;
        $amount_to_default = round($request_details['amount'] / $currency, 2);
        //get wallet balance
        $last_amount = $this->notificationClass->getWalletBalance($provider_id);
        $order_no = $payment_transaction_id !== "" ? $payment_transaction_id : $wallet_holder_name;
        // For card payments, wallet credit must come from webhook confirmation only.
        if (!$is_card_payment && $request_details['amount'] > 0){
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $provider_id;
            $add_balance->wallet_provider_type = $provider_type;
            $add_balance->transaction_type = 1;
            $add_balance->amount = $amount_to_default;
            $add_balance->order_no = $wallet_holder_name;
            //$add_balance->request_amount = round($request->get('amount'), 2);
            $add_balance->subject = "credit by " . $wallet_holder_name;
            $add_balance->subject_code = 1;
            $add_balance->remaining_balance = floatval($last_amount + $amount_to_default);
            $add_balance->save();
            $last_amount = $add_balance->remaining_balance;
        }

        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "wallet_balance" => round($last_amount * $currency, 2),
            "payment_status" => $payment_status,
            "payment_transaction_id" => $payment_transaction_id,
            "redirect_url" => $redirect_url,
            "success_url" => $success_url,
            "failed_url" => $failed_url,
            "error_url" => ""
        ]);
    }

    private function wompiCreateCheckoutUrl($general_settings, $customer_email, $amount, $provider_id = 0, $provider_type = 0)
    {
        $is_sandbox = (int)($general_settings->wompi_mode ?? 0) === 0;
        $public_key = $is_sandbox ? ($general_settings->wompi_sandbox_public_key ?? '') : ($general_settings->wompi_production_public_key ?? '');
        $integrity_key = $is_sandbox ? ($general_settings->wompi_sandbox_integrity_key ?? '') : ($general_settings->wompi_production_integrity_key ?? '');
        $public_key = preg_replace('/\s+/', '', (string)$public_key);
        $integrity_key = preg_replace('/\s+/', '', (string)$integrity_key);
        $success_url = \Illuminate\Support\Facades\Route::has('payment.success') ? route('payment.success') : "";
        $failed_url = \Illuminate\Support\Facades\Route::has('payment.failed') ? route('payment.failed') : "";
        $redirect_after_payment = \Illuminate\Support\Facades\Route::has('payment.wompi.redirect')
            ? route('payment.wompi.redirect')
            : ($success_url !== "" ? $success_url : $failed_url);

        if ($public_key === '' || $integrity_key === '') {
            return [
                "status" => 0,
                "message" => __('user_messages.378'),
                "message_code" => 378,
            ];
        }

        $amount_in_cents = (int) round(((float) $amount) * 100);
        $reference = 'XISTI-WALLET-U' . (int)$provider_id . '-P' . (int)$provider_type . '-' . time() . '-' . rand(1000, 9999);
        $signature = hash('sha256', $reference . $amount_in_cents . 'COP' . $integrity_key);

        $query = http_build_query([
            'public-key' => $public_key,
            'currency' => 'COP',
            'amount-in-cents' => $amount_in_cents,
            'reference' => $reference,
            'signature:integrity' => $signature,
            'redirect-url' => $redirect_after_payment,
            'customer-data:email' => strtolower(trim((string)$customer_email)),
        ], '', '&', PHP_QUERY_RFC3986);

        return [
            "status" => 1,
            "message" => "success",
            "payment_status" => "PENDING",
            "payment_transaction_id" => "",
            "reference" => $reference,
            "redirect_url" => 'https://checkout.wompi.co/p/?' . $query,
            "success_url" => $success_url,
            "failed_url" => $failed_url,
            "error_url" => ""
        ];
    }

    public function processWompiWebhook(array $payload)
    {
        $general_settings = GeneralSettings::query()->first();
        if ($general_settings == null) {
            return ["status" => 0, "message" => __('user_messages.9'), "message_code" => 9];
        }

        if (!$this->verifyWompiWebhookSignature($payload, $general_settings)) {
            return ["status" => 0, "message" => __('user_messages.379'), "message_code" => 379];
        }

        $transaction = (array)($payload['data']['transaction'] ?? []);
        if (empty($transaction) && isset($payload['data']) && is_array($payload['data'])) {
            $transaction = (array)$payload['data'];
        }

        $transaction_id = trim((string)($transaction['id'] ?? ($payload['transaction_id'] ?? '')));
        $reference = trim((string)($transaction['reference'] ?? ''));
        $payment_status = strtoupper(trim((string)($transaction['status'] ?? '')));
        $amount_in_cents = (int)($transaction['amount_in_cents'] ?? 0);

        // Some webhook events send only transaction id. Resolve full transaction from Wompi API.
        if ($transaction_id !== '' && ($reference === '' || $payment_status === '' || $amount_in_cents <= 0)) {
            $resolved = $this->wompiGetTransactionDetails($general_settings, $transaction_id);
            if (!empty($resolved)) {
                $reference = $reference !== '' ? $reference : (string)($resolved['reference'] ?? '');
                $payment_status = $payment_status !== '' ? $payment_status : strtoupper((string)($resolved['status'] ?? ''));
                $amount_in_cents = $amount_in_cents > 0 ? $amount_in_cents : (int)($resolved['amount_in_cents'] ?? 0);
            }
        }

        if ($transaction_id === '' || $reference === '' || $payment_status === '') {
            return ["status" => 0, "message" => __('user_messages.380'), "message_code" => 380];
        }

        if ($payment_status !== 'APPROVED') {
            return [
                "status" => 1,
                "message" => "Webhook received for status: " . $payment_status,
                "message_code" => 1,
                "payment_status" => $payment_status,
                "payment_transaction_id" => $transaction_id,
            ];
        }

        if (!$this->creditWalletFromWebhookReference($reference, $transaction_id, $amount_in_cents)) {
            return ["status" => 0, "message" => "Unable to map transaction reference", "message_code" => 9];
        }

        return [
            "status" => 1,
            "message" => "Webhook processed",
            "message_code" => 1,
            "payment_status" => $payment_status,
            "payment_transaction_id" => $transaction_id,
        ];
    }

    private function verifyWompiWebhookSignature(array $payload, $general_settings)
    {
        $is_sandbox = (int)($general_settings->wompi_mode ?? 0) === 0;
        $event_key = $is_sandbox ? ($general_settings->wompi_sandbox_event_key ?? '') : ($general_settings->wompi_production_event_key ?? '');
        $event_key = trim((string)$event_key);
        if ($event_key === '') {
            Log::warning('wompi.webhook.missing_event_key', [
                'sandbox' => $is_sandbox,
            ]);

            return false;
        }

        $signature = (array)($payload['signature'] ?? []);
        $checksum = strtolower(trim((string)($signature['checksum'] ?? '')));
        $properties = (array)($signature['properties'] ?? []);
        $timestamp = (string)($payload['timestamp'] ?? '');

        if ($checksum === '' || empty($properties) || $timestamp === '') {
            return false;
        }

        $raw = '';
        foreach ($properties as $path) {
            $path = (string)$path;
            $resolved_path = str_starts_with($path, 'data.') ? $path : ('data.' . $path);
            $raw .= (string)$this->readWebhookPath($payload, $resolved_path);
        }
        $raw .= $timestamp . $event_key;
        $expected = strtolower(hash('sha256', $raw));

        return hash_equals($expected, $checksum);
    }

    private function readWebhookPath(array $payload, $path)
    {
        $segments = explode('.', (string)$path);
        $current = $payload;
        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return '';
            }
            $current = $current[$segment];
        }

        return is_scalar($current) ? (string)$current : '';
    }

    private function creditWalletFromWebhookReference($reference, $transaction_id, $amount_in_cents)
    {
        if (!preg_match('/^XISTI-WALLET-U(\d+)-P(\d+)-/i', $reference, $matches)) {
            return false;
        }

        $provider_id = (int)$matches[1];
        $provider_type = (int)$matches[2];

        $already_credited = UserWalletTransaction::query()
            ->where('user_id', $provider_id)
            ->where('wallet_provider_type', $provider_type)
            ->where('transaction_type', 1)
            ->where('order_no', $transaction_id)
            ->exists();
        if ($already_credited) {
            return true;
        }

        $user = User::query()->select('id', 'email', 'currency','first_name')->where('id', $provider_id)->first();
        if ($user == null) {
            return false;
        }

        $provider_currency = WorldCurrency::query()->where('symbol', $user->currency)->first();
        if ($provider_currency == Null) {
            $provider_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency_ratio = $provider_currency != Null ? (float)$provider_currency->ratio : 1.0;
        $amount = ((float)$amount_in_cents) / 100;
        $amount_to_default = round($amount / ($currency_ratio > 0 ? $currency_ratio : 1), 2);
        $last_amount = $this->notificationClass->getWalletBalance($provider_id);

        $add_balance = new UserWalletTransaction();
        $add_balance->user_id = $provider_id;
        $add_balance->wallet_provider_type = $provider_type;
        $add_balance->transaction_type = 1;
        $add_balance->amount = $amount_to_default;
        $add_balance->order_no = strtolower(trim((string)$user->first_name));
        $add_balance->subject = "credit by " . strtolower(trim((string)$user->first_name));
        $add_balance->subject_code = 1;
        $add_balance->remaining_balance = floatval($last_amount + $amount_to_default);
        $add_balance->save();

        return true;
    }

    private function wompiGetTransactionDetails($general_settings, $transaction_id)
    {
        $is_sandbox = (int)($general_settings->wompi_mode ?? 0) === 0;
        $base_url = $is_sandbox
            ? (($general_settings->wompi_sandbox_base_url ?? '') ?: 'https://sandbox.wompi.co/v1')
            : (($general_settings->wompi_production_base_url ?? '') ?: 'https://production.wompi.co/v1');
        $public_key = $is_sandbox ? ($general_settings->wompi_sandbox_public_key ?? '') : ($general_settings->wompi_production_public_key ?? '');
        $public_key = preg_replace('/\s+/', '', (string)$public_key);
        if ($public_key === '') {
            return [];
        }

        $response = $this->wompiCurl(
            'GET',
            rtrim((string)$base_url, '/') . '/transactions/' . trim((string)$transaction_id),
            ['Authorization: Bearer ' . $public_key]
        );

        return (array)($response['body']['data'] ?? []);
    }

    private function wompiCreatePaymentSource($general_settings, $customer_email, $request_details)
    {
        $is_sandbox = (int)($general_settings->wompi_mode ?? 0) === 0;
        $base_url = $is_sandbox
            ? (($general_settings->wompi_sandbox_base_url ?? '') ?: 'https://sandbox.wompi.co/v1')
            : (($general_settings->wompi_production_base_url ?? '') ?: 'https://production.wompi.co/v1');
        $public_key = $is_sandbox ? ($general_settings->wompi_sandbox_public_key ?? '') : ($general_settings->wompi_production_public_key ?? '');
        $private_key = $is_sandbox ? ($general_settings->wompi_sandbox_private_key ?? '') : ($general_settings->wompi_production_private_key ?? '');

        if ($public_key === '' || $private_key === '') {
            return [
                "status" => 0,
                "message" => __('user_messages.381'),
                "message_code" => 381,
            ];
        }

        $merchant = $this->wompiCurl(
            'GET',
            rtrim($base_url, '/') . '/merchants/' . $public_key,
            ['Authorization: Bearer ' . $public_key]
        );
        $acceptance_token = $merchant['body']['data']['presigned_acceptance']['acceptance_token'] ?? '';
        $accept_personal_auth = $merchant['body']['data']['presigned_personal_data_auth']['acceptance_token'] ?? '';
        if ($acceptance_token === '') {
            return [
                "status" => 0,
                "message" => __('user_messages.382'),
                "message_code" => 382,
            ];
        }

        $year = (string) $request_details['year'];
        $exp_year = strlen($year) > 2 ? substr($year, -2) : str_pad($year, 2, '0', STR_PAD_LEFT);
        $exp_month = str_pad((string) $request_details['month'], 2, '0', STR_PAD_LEFT);
        $card_token_response = $this->wompiCurl(
            'POST',
            rtrim($base_url, '/') . '/tokens/cards',
            ['Authorization: Bearer ' . $public_key, 'Content-Type: application/json'],
            [
                'number' => (string) $request_details['card_number'],
                'cvc' => (string) $request_details['cvv'],
                'exp_month' => $exp_month,
                'exp_year' => $exp_year,
                'card_holder' => (string) $request_details['holder_name'],
            ]
        );

        $card_token = $card_token_response['body']['data']['id'] ?? '';
        if ($card_token === '') {
            return [
                "status" => 0,
                "message" => __('user_messages.383'),
                "message_code" => 383,
            ];
        }

        $payload = [
            'type' => 'CARD',
            'token' => $card_token,
            'customer_email' => $customer_email,
            'acceptance_token' => $acceptance_token,
        ];
        if ($accept_personal_auth !== '') {
            $payload['accept_personal_auth'] = $accept_personal_auth;
        }

        $payment_source = $this->wompiCurl(
            'POST',
            rtrim($base_url, '/') . '/payment_sources',
            ['Authorization: Bearer ' . $private_key, 'Content-Type: application/json'],
            $payload
        );
        $payment_source_id = $payment_source['body']['data']['id'] ?? null;
        if ($payment_source_id == null) {
            return [
                "status" => 0,
                "message" => __('user_messages.384'),
                "message_code" => 384,
            ];
        }

        return [
            "status" => 1,
            "payment_source_id" => $payment_source_id,
            "brand" => (string)($card_token_response['body']['data']['brand'] ?? ''),
        ];
    }

    private function wompiCurl($method, $url, $headers = [], $payload = null)
    {
        $ch = curl_init();
        if ($method === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return [
            'raw' => $response,
            'body' => json_decode($response, true),
        ];
    }


    //Get Wallet Transaction History List Code
    public function getWalletTransactionList($provider_type, $provider_id, $currency, $user_language = 'en',$order_by=0,$date_filter=0,$page = 0,$per_page = 10){

        //filter date => 0:all,1:toady,2:7 days,3:30 days,4:this year,5:Last year
        //order_by => 0:all,1:credit,2:debit
        //service_category => null,not null for filter cat wise
        $user_currency = WorldCurrency::query()->where('symbol', $currency)->first();
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency != Null ? $user_currency->ratio : 1;


        $walletTransaction = config('wallettransaction.'.$user_language);

        $filter_type = $date_filter;
        $date = date('Y-m-d');
        if ($filter_type == 1) {
            //today
            $start_date = $date . " 00:00:01";
            $end_date = $date . " 23:59:59";
        } elseif ($filter_type == 2) {
            //last 7 day
            $start_date = date('Y-m-d', strtotime('-7 days', strtotime($date)));
            // $start_date = date('Y-m-d', strtotime($date . ' - 7 days'));
            $end_date = $date;
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        } elseif ($filter_type == 3) {
            //last 30 day
            $start_date = date('Y-m-d', strtotime('-30 days', strtotime($date)));
            $end_date = $date;
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        } elseif ($filter_type == 4) {
            //this year
            $start_date = date("Y-01-01", strtotime($date));
            $end_date = date("Y-m-d", strtotime($date));
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        } elseif ($filter_type == 5) {//$filter_type == 0//all order
            //last 365 day
            $start_date = date('Y-m-d', strtotime('-365 days', strtotime($date)));
            $end_date = $date;
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        }else{
            $start_date = "";
            $end_date = "";
        }

        //pagination
        $per_page = 10;
        if ($page > 0) {
            $per_page = $per_page;
        }
        $transactions_list = UserWalletTransaction::query()->select(
            'user_wallet_transaction.id',
            DB::raw('ROUND(user_wallet_transaction.amount * ' . $currency . ',2) As amount'),
            'user_wallet_transaction.transaction_type',
            'user_wallet_transaction.subject',
            'user_wallet_transaction.subject_code',
            'user_wallet_transaction.order_no',
            'user_wallet_transaction.remaining_balance' ,
            'user_wallet_transaction.created_at as date_time'
        )
            ->where('wallet_provider_type', "=", $provider_type)
            ->where('user_id', "=", $provider_id);
            if($order_by > 0){
                $transactions_list = $transactions_list->where('transaction_type', "=", $order_by);
            }
            if($date_filter > 0){
                $transactions_list= $transactions_list->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date);
            }
        $transactions_list = $transactions_list->orderBy('id', 'desc')->paginate($per_page);
        $current_page = $transactions_list->currentPage();
        $last_page = $transactions_list->lastPage();
        $total = $transactions_list->total();
        $transactions = [];
        foreach ($transactions_list as $key => $transactions_details){
            $transactions[] = [
                "id" => $transactions_details->id,
                "amount" => $transactions_details->amount,
                "transaction_type" => $transactions_details->transaction_type,
                "subject" => $transactions_details->subject_code == null ? $transactions_details->subject :$walletTransaction[$transactions_details->subject_code]." ".$transactions_details->order_no,
                "remaining_balance" => $transactions_details->remaining_balance,
                "date_time" => $transactions_details->date_time,
            ];
        }

        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "transactions" => $transactions,
            "current_page" => $current_page,
            "last_page" => $last_page,
            "total_rec" => $total,
        ]);
    }

    //Get Wallet Balance Code
    public function getWalletBalance($provider_type, $provider_id, $currency){
        $settings = request()->get("general_settings");
        $user_currency = WorldCurrency::query()->where('symbol', $currency)->first();
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency != Null ? $user_currency->ratio : 1;
        $wallet_balance = UserWalletTransaction::query()->where('wallet_provider_type', '=', $provider_type)->where('user_id', '=', $provider_id)->orderBy('id', 'desc')->first();
        if ($wallet_balance != Null) {
            $balance = round($wallet_balance->remaining_balance * $currency, 2);
        } else {
            $balance = 0;
        }

        //get top up balance
        $get_topup_wallet = TopUpWallet::query()
            ->select('id','name',DB::raw('ROUND(value  * ' . $currency . ',2) As package_price'))
            ->get()->toArray();

        $minWompiTopup = (float) ($settings->driver_min_amount ?? 13000);

        return response()->json([
            "status" => 1,
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "wallet_balance" => $balance,
            "topup_wallet" => $get_topup_wallet,
            "is_auto_settle" => $settings->auto_settle_wallet,
            "min_wompi_topup_amount" => round($minWompiTopup * $currency, 2),
        ]);
    }

    //Search Wallet Transfer User List Code
    public function searchWalletTransferUserList($provider_type, $provider_id, $search){

        $user_profile_url = url('/assets/images/profile-images/customer');
        $user_list = User::query()
            ->select("users.id as transfer_id", "users.first_name as name", "users.email", DB::raw("(CASE WHEN users.avatar != '' THEN (CASE WHEN CHAR_LENGTH(users.avatar) >= 25 THEN users.avatar ELSE concat('$user_profile_url','/',users.avatar) END) ELSE '' END) as profile_image"), "users.country_code as country_code", "users.contact_number as contact_number", DB::raw($this->user_type.' AS wallet_provider_type'))
            ->where('users.is_register', "=", 1)
            ->where('users.status', "=", 1);
        if ($provider_type == $this->user_type) {
            $user_list->where('users.id', "!=", $provider_id);
        }
        $user_list = $user_list->where(function ($query) use ($search) {
            $query->orWhere('users.email', 'LIKE', "%{$search}%");
            $query->orWhere('users.first_name', 'LIKE', "%{$search}%");
            $query->orWhere(DB::raw("CONCAT_WS(' ', users.first_name, users.last_name)"), 'LIKE', "%{$search}%");
            $query->orWhere(DB::raw('CONCAT_WS("", users.country_code, users.contact_number)'), 'LIKE', '%' . $search . '%');
        })
            ->whereNull('users.deleted_at')
            ->groupBy('users.id')
            ->get()->toArray();

        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "transfer_user_list" => $user_list
        ]);
    }

    //Wallet to Wallet Transfer Manage Code
    public function walletToWalletTransfer($provider_type, $provider_id, $wallet_holder_name, $currency, $request_details){

        $provider_currency = WorldCurrency::query()->where('symbol', $currency)->first();
        if ($provider_currency == Null) {
            $provider_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $provider_currency != Null ? $provider_currency->ratio : 1;
        $currency_code = $provider_currency != Null ? $provider_currency->currency_code : '';

        $amount_to_default = round($request_details['amount'] / $currency, 2);

        //get wallet balance
        $last_amount = $this->notificationClass->getWalletBalance($provider_id);
        if ($request_details['amount'] > 0) {
            if ($amount_to_default > $last_amount) {
                return response()->json([
                    "status" => 0,
//                    "message" => "You can't transfer amount through Wallet because your wallet balance is insufficient.",
                    'message' => __('user_messages.110'),
                    "message_code" => 110
                ]);
            }
            $transfer_id = $request_details['transfer_id'];
            $wallet_provider_type = $request_details['wallet_provider_type'];


            if (in_array($wallet_provider_type, [$this->driver_type])) {
                $transfer_provider_user_details = Provider::query()
                    ->select("providers.id as id", "providers.first_name as name", "providers.device_token as device_token", "providers.login_device as login_device", "providers.language")
                    ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
                    ->join('service_category', 'service_category.id', '=', 'provider_services.service_cat_id')
                    ->where('providers.status', '=', 1)
                    ->where('provider_services.status', '=', 1)
                    ->whereNull('providers.deleted_at')
                    ->where('providers.id', "=", $transfer_id)
                    ->first();
            }
            elseif ($wallet_provider_type == $this->user_type) {
                $transfer_provider_user_details = User::query()
                    ->select("users.id as id", "users.first_name as name", "users.device_token as device_token", "users.login_device as login_device","users.language")
                    ->where('users.status', "=", 1)
                    ->whereNull('users.deleted_at')
                    ->where('users.id', "=", $transfer_id)
                    ->first();

                $title = __('user_messages.262',[],$transfer_provider_user_details->language);
                $message = __('user_messages.263',[],$transfer_provider_user_details->language);

            }
            else {
                return response()->json([
                    "status" => 0,
//                    "message" => "Invalid wallet transfer type",
                    'message' => __('user_messages.9'),
                    "message_code" => 9
                ]);
            }
            if ($transfer_provider_user_details == Null) {
                return response()->json([
                    "status" => 0,
//                    "message" => "App Transfer User Not Found",
                    'message' => __('user_messages.5'),
                    "message_code" => 5
                ]);
            }

            $transfer_id = $transfer_provider_user_details->id;
            $transfer_wallet_holder_name = $transfer_provider_user_details->name . "";
            $transfer_wallet_holder_device_token = $transfer_provider_user_details->device_token;
            $transfer_wallet_holder_login_device = $transfer_provider_user_details->login_device;

            //get wallet balance
            $transfer_last_amount = $this->notificationClass->getWalletBalance($transfer_id);

           //for Wallet Amount Debit Transaction Code
            $debited_balance = new UserWalletTransaction();
            $debited_balance->user_id = $provider_id;
            $debited_balance->wallet_provider_type = $provider_type;
            $debited_balance->transaction_type = 2;
            $debited_balance->amount = $amount_to_default;
            $debited_balance->subject = "Wallet Amount Transfer to " . $transfer_wallet_holder_name;
            $debited_balance->remaining_balance = floatval($last_amount - $amount_to_default);
            $debited_balance->subject_code = 8;
            $debited_balance->order_no = $transfer_wallet_holder_name;
            $debited_balance->save();

            //for Wallet Amount Credit Transaction Code
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $transfer_id;
            $add_balance->wallet_provider_type = $wallet_provider_type;
            $add_balance->transaction_type = 1;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject = "credit by " . $wallet_holder_name;
            $add_balance->remaining_balance = floatval($transfer_last_amount + $amount_to_default);
            $add_balance->subject_code = 1;
            $add_balance->order_no = $wallet_holder_name;
            $add_balance->save();
            if ($transfer_wallet_holder_device_token != Null && $transfer_wallet_holder_login_device != Null){
               $user_wallet_notication = $this->notificationClass->userWalletTransferNotification($title,$message,$transfer_wallet_holder_device_token,$transfer_wallet_holder_login_device,$amount_to_default);
            }
        }
        else {
            return response()->json([
                "status" => 0,
//                "message" => "amount can`t be null.",
                'message' => __('user_messages.9'),
                "message_code" => 9
            ]);
        }
        return response()->json([
            "status" => 1,
//            "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
        ]);

    }

    //For Get Driver Required Document List Code
    public function getDriverRequiredDocumentList($user_id , $doc_status , $driver_type)
    {
        $required_documents = RequiredDocuments::query()->where('status', 1)->get();
        $driverDetails = TransportDriverDetails::query()->where('user_id', $user_id)->first();
        $deliveryVariant = $driverDetails?->delivery_variant ?? null;
        $registrationKey = null;
        if ($driverDetails && Schema::hasTable('transport_vehicle_type')) {
            $typeName = DB::table('transport_vehicle_type')
                ->where('id', $driverDetails->vehicle_type_id)
                ->value('name');
            $registrationKey = strtolower((string) $typeName);
        }
        $required_documents = collect(
            VehicleDocumentRules::filterForVehicle($required_documents, $registrationKey, $deliveryVariant)
        );
        $document_list = [];
        if ($required_documents->isEmpty()) {
            User::query()
                ->where('id', $user_id)
                ->where('driver_doc_status', '!=', 1)
                ->update(['driver_doc_status' => 1]);
            $doc_status = 1;
        } elseif ($required_documents != Null) {
            foreach ($required_documents as $document) {
                $get_driver_document = ProviderDocuments::query()->where('user_id', $user_id)->where('req_document_id', $document->id)->first();
                $document_list[] = [
                    'document_id' => $document->id,
                    'document_name' => $document->name,
                    'document_file' => $get_driver_document != Null ? url('/assets/images/provider-documents/' . $get_driver_document->document_file) : '',
                    'contains_expiry' => ($document->contains_expiry == null) ? 0 : $document->contains_expiry,
                    'document_expire_date' => ($get_driver_document && $get_driver_document->expiry_date != null) ? Carbon::parse($get_driver_document->expiry_date)->format('Y-m-d') : "",
                    'document_status' => $get_driver_document != Null ? $get_driver_document->status : 4,
                    'supports_national_id_sides' => VehicleDocumentRules::supportsNationalIdSides((string) $document->name) ? 1 : 0,
                ];
            }
            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                "driver_doc_status" => $doc_status - 0,
                "is_driver_type" => $driver_type,
                "document_list" => $document_list,
            ]);
        } else {
            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                "driver_doc_status" => $doc_status - 0,
                "is_driver_type" => $driver_type,
                "document_list" => $document_list,
            ]);
        }
    }

    /**
     * When a user deletes their account, any positive wallet balance is forfeited to the platform
     * (recorded as a debit with remaining_balance = 0).
     */
    public function forfeitWalletBalanceOnAccountDeletion(int $userId): float
    {
        $totalForfeited = 0.0;
        $walletTypes = [$this->user_type, $this->driver_type];

        foreach ($walletTypes as $walletProviderType) {
            $lastTransaction = UserWalletTransaction::query()
                ->where('user_id', $userId)
                ->where('wallet_provider_type', $walletProviderType)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastTransaction === null) {
                continue;
            }

            $balance = round((float) $lastTransaction->remaining_balance, 2);
            if ($balance <= 0) {
                continue;
            }

            $debit = new UserWalletTransaction();
            $debit->user_id = $userId;
            $debit->wallet_provider_type = $walletProviderType;
            $debit->transaction_type = 2;
            $debit->amount = $balance;
            $debit->subject = 'Account deletion - balance retained by platform';
            $debit->subject_code = 19;
            $debit->remaining_balance = 0;
            $debit->order_no = '';
            $debit->save();

            $totalForfeited += $balance;
        }

        return round($totalForfeited, 2);
    }

}
