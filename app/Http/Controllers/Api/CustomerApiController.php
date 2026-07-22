<?php

namespace App\Http\Controllers\Api;

use App\Classes\AdminClass;
use App\Classes\NotificationClass;
use App\Classes\UserClassApi;
use App\Http\Requests\DeviceTokenRequest;
use App\Models\AdminAreaList;
use App\Models\AppVersionSetting;
use App\Models\AreawiseServiceCategory;
use App\Models\CashOut;
use App\Models\EmailTemplates;
use App\Models\General_Setting;
use App\Models\GeneralSettings;
use App\Models\LanguageLists;
use App\Models\PageSettings;
use App\Models\PromocodeDetails;
use App\Models\Provider;
use App\Models\ProviderServices;
use App\Models\PushNotification;
use App\Models\ServiceCategory;
use App\Models\Sos;
use App\Models\TempUserBooking;
use App\Models\TransportCourierDetails;
use App\Models\TransportDriverDetails;
use App\Models\TransportRideBook;
use App\Models\UsedPromocodeDetails;
use App\Models\User;
use App\Models\UserRideWayPoint;
use Exception;
use App\Models\UserAddress;
use App\Models\UserCardDetails;
use App\Helpers\RideSessionHelper;
use App\Models\UserRunningRide;
use App\Models\UserWalletTransaction;
use App\Models\VehicleService;
use App\Helpers\ServiceCatalogHelper;
use App\Models\WorldCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
//use Mockery\Exception;

class CustomerApiController extends Controller
{
//        json response status [
//            0 => false,
//            1 => true,
//            2 => registration pending,
//            3 => app user blocked,
//            4 => app user access token not match,
//            5 => app user not found
//          ]

    private $userClassapi;
    private $adminClass;
    private $notificationClass;
    private $user_type = 0;
    private $driver_type = 2;

    public function __construct(UserClassApi $userClassapi, AdminClass $adminClass, NotificationClass $notificationClass)
    {
        $this->userClassapi = $userClassapi;
        $this->adminClass = $adminClass;
        $this->notificationClass = $notificationClass;
    }

    public function postTestEmailTemplates(Request $request)
    {
        //echo date('Y-m-d H:i:s');exit;
        $email_templates = EmailTemplates::query()->where('id', 2)->where('status', 1)->first();
        if ($email_templates != Null) {
            $items = ['%NAME%', '%LINK%', '%ADMIN%'];
            $replace = ['kkcoder', 'https://www.google.com', 'Froyo Tech Inc'];
            $html = str_replace($items, $replace, $email_templates->content);

            echo $html;
        }
    }

    public function postWompiWebhook(Request $request)
    {
        $forwardSecret = trim((string) config('services.wompi.forward_secret', ''));
        if ($forwardSecret !== '' && !hash_equals($forwardSecret, (string) $request->header('X-Wompi-Forward-Secret', ''))) {
            \Log::warning('wompi_webhook_rejected_unauthorized_forward', [
                'reference' => data_get($request->all(), 'data.transaction.reference'),
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized',
                'message_code' => 401,
            ], 401);
        }

        $payload = $request->all();
        if (empty($payload)) {
            $raw = (string)$request->getContent();
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $eventName = (string) data_get($payload, 'event', data_get($payload, 'data.transaction.status', 'unknown'));
        \Log::info('wompi_webhook_received', [
            'event' => $eventName,
            'reference' => data_get($payload, 'data.transaction.reference'),
            'transaction_id' => data_get($payload, 'data.transaction.id'),
        ]);
        $result = $this->userClassapi->processWompiWebhook($payload);
        return response()->json($result, 200);
    }

    public function postHomepage(Request $request){
        $this->notificationClass->ApiLogDetail(0, $request->get('user_id'), "postHomepage", $request->all());

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "app_version" => "required",
            "current_lat" => "nullable",
            "current_long" => "nullable",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_details = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_details)) {
            return $failed;
        }
        $user_details = User::query()->where("id", "=", $user_details->id)->first();
        if ($user_details == Null) {
            return response()->json([
                'status' => 5,
                //'message' => "User not found!",
                'message' => __('user_messages.5'),
                "message_code" => 5,
            ]);
        }
        $language = $user_details->language;
        if ($language != "en" && $language != "" && $language != "Null") {
            $lang_prefix = $language . "_";
        } else {
            $lang_prefix = "";
        }

        $user_currency = \App\Support\UserCurrencyResolver::forCurrency($user_details->currency);
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency->ratio;

        $user_details->app_version = $request->get("app_version");

        $current_lat = $request->get("current_lat");
        $current_long = $request->get("current_long");
        $area_id = 0;
        if ($current_lat != Null && $current_long != Null) {
            $get_admin_area_list = AdminAreaList::query()->where('status', 1)->get();
            if ($get_admin_area_list->isNotEmpty()) {
                foreach ($get_admin_area_list as $get_area) {
                    $vertices_x = explode(",", $get_area->latitude);
                    $vertices_y = explode(",", $get_area->longitude);

                    $points_polygon = count($vertices_x) - 1;
                    $longitude_x = $current_lat;
                    $latitude_y = $current_long;

                    if ($this->adminClass->is_in_restricted_area($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)) {
                        $area_id = $get_area->id;
                        break;
                    }
                }
                $user_details->area_id = $area_id;
            }
        }
        $user_details->save();

        $services = VehicleService::query()
            ->select(ServiceCatalogHelper::homeServiceSelect($lang_prefix, (float) $currency))
            ->where('status', 1)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->toArray();

        $services = \App\Helpers\MobileFeatureFlagsHelper::filterServiceRows($services);
        $services = \App\Helpers\DeliveryVehicleHelper::filterHomeServiceRows($services);
        $service_modes = ServiceCatalogHelper::buildServiceModesFromRows($services, $user_details->language ?? 'es');

        $general_settings = request()->get("general_settings");
        $userLanguage = $user_details->language ?? 'es';

        return response()->json(array_merge([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "services" => $services,
            "service_modes" => $service_modes,
            "delivery_vehicle_options" => \App\Helpers\DeliveryVehicleHelper::deliveryOptionsForApi($lang_prefix),
            "delivery_passenger_disclaimer" => \App\Helpers\DeliveryVehicleHelper::passengerDisclaimer($userLanguage),
            "encomienda_passenger_disclaimer" => 'Indica qué comprar, el tope de precio, dónde comprar y dónde entregar.',
            "is_driver_type" => $user_details->is_driver_type,
            "is_driver_status" => $user_details->is_driver_status,
            "driver_doc_status" => $user_details->driver_doc_status,
            "driver_vehicle_status" => $user_details->driver_vehicle_status,
            "cash_payment" => $general_settings->cash_payment != NUll ? $general_settings->cash_payment: 0,
            "card_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
            "online_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
            "wallet_payment" => $general_settings->wallet_payment != NUll ? $general_settings->wallet_payment: 0,
        ], \App\Helpers\AppMobileSettingsHelper::pricingAndCommissionPayload($general_settings)));
    }

    public function postCustomerGetRunningService(Request $request)
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
        $user_details = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_details)) {
            return $failed;
        }
        $extras = [];
        $recipient_name = '';
        $recipient_contact_number = '';
        $item_description = '';
        $estimate_price = 0;

        $ride_details = RideSessionHelper::activePassengerRide((int) $user_details->id, $this->notificationClass);
        if ($ride_details !== null) {

            $destination_coordinates = explode(',', $ride_details->destination_latlong);
            $address_list = array();
            $address_list[] = [
                "address" => $ride_details->pickup_address,
                "address_lat" => trim($ride_details->pickup_lat),
                "address_long" => trim($ride_details->pickup_long)
            ];
            if ($ride_details->is_way_point == 1) {
                $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride_details->id)->first();
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
                "address" => $ride_details->destination_address,
                "address_lat" => trim($destination_coordinates[0] ?? Null),
                "address_long" => trim($destination_coordinates[1] ?? Null)
            ];
            if ($ride_details->status == 9) {
                $ride_completed_status = 1;
                $ride_cancelled_status = 0;
            } elseif ($ride_details->status == 4) {
                $ride_completed_status = 0;
                $ride_cancelled_status = 1;
            } else {
                $ride_completed_status = 0;
                $ride_cancelled_status = 0;
            }

            if($ride_details->vehicle_service_id == 4){
                $courier_details = TransportCourierDetails::query()->where('ride_id',$ride_details->id)->first();
                if($courier_details != Null){
                    $recipient_name = $courier_details->recipient_name;
                    $recipient_contact_number = $courier_details->recipient_contact_number;
                    $item_description = $courier_details->item_description;
                    $estimate_price = $courier_details->estimate_price;
                }
            }
            $user_currency = \App\Support\UserCurrencyResolver::forCurrency($user_details->currency);
            if ($user_currency == Null) {
                $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
            }
            $currency = $user_currency->ratio;

            if($ride_details->status == 0){
                $extras[]=[
                    "ride_id" => $ride_details->id,
                    "ride_status" => $ride_details->status,
                    'offered_price' => round($ride_details->offered_price * $currency, 2),
                    'service_id' => $ride_details->vehicle_service_id,
                    'recipient_name' => $recipient_name,
                    'recipient_contact_number' => $recipient_contact_number,
                    'item_description' => $item_description,
                    'estimate_price' => $estimate_price,
                    'min_bargain_amt' => round($ride_details->min_bargain_amt * $currency, 2),
                    'max_bargain_amt' => round($ride_details->max_bargain_amt * $currency, 2),
                    "address_list" => $address_list,
                ];
            }
            $general_settings = request()->get("general_settings");


            return response()->json([
                "status" => 1,
//                "message" => 'success!',
                'message' => __('user_messages.1'),
                "message_code" => 1,
                "ride_id" => $ride_details->id,
                "ride_status" => $ride_details->status,
                "ride_completed_status" => $ride_completed_status,
                "ride_cancelled_status" => $ride_cancelled_status,
                "cash_payment" => $general_settings->cash_payment != NUll ? $general_settings->cash_payment: 0,
                "card_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
                "online_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
                "wallet_payment" => $general_settings->wallet_payment != NUll ? $general_settings->wallet_payment: 0,
                "ride_details" => $extras
            ]);
        }
        return response()->json([
            "status" => 0,
            "message" => 'service not running!',
            "message_code" => 1,
        ]);
    }

    public function postCustomerAddCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "holder_name" => "nullable",
            "card_number" => "required",
            "month" => "required|numeric",
            "year" => "required|numeric",
            "cvv" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->addCardManage($this->user_type, $request->get('user_id'), $request->all());
    }

    public function postCustomerRemoveCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "card_id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_id = $request->get("user_id");
        $card_id = $request->get("card_id");
        $user_check = $this->userClassapi->checkUserAllow($user_id, $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        return $this->userClassapi->deleteCardManage($this->user_type, $user_id, $card_id);
    }

    public function postCustomerCardList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        return $this->userClassapi->manageCardList($this->user_type, $request->get('user_id'));
    }

    public function postCustomerAddWalletBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "amount" => "required|numeric",
            "payment_method_type" => "nullable|in:1,0",
            "card_id" => "nullable|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_details = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_details)) {
            return $failed;
        }
        $customer_email = strtolower(trim((string)$user_details->email));
        return $this->userClassapi->addWalletBalance($this->user_type, $request->get('user_id'), $customer_email, $user_details->currency, $request->all());
    }

    public function postCustomerWalletTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "order_by" => "required|numeric|in:0,1,2",
            "date_filter" => "required|numeric|in:0,1,2,3,4,5",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        //filter date => 0:all,1:toady,2:7 days,3:30 days,4:this year,5:Last year
        //order_by => 0:all,1:credit,2:debit
        $user_id = $request->get('user_id');
        $access_token = $request->get('access_token');
        $order_by = $request->get('order_by');
        $date_filter = $request->get('date_filter');
        $page = $request->get('page');
        $per_page = $request->get('per_page');

        $user_check = $this->userClassapi->checkUserAllow($user_id, $access_token);
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        $user_language = $user_check->language == null ? 'en' : $user_check->language;
        return $this->userClassapi->getWalletTransactionList($this->user_type, $user_id, $user_check->currency, $user_language,$order_by,$date_filter,$page,$per_page);
    }

    public function postCustomerGetWalletBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->getWalletBalance($this->user_type, $request->get('user_id'), $user_check->currency);
    }

    public function postCustomerSearchWalletTransferUserList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "search" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        return $this->userClassapi->searchWalletTransferUserList($this->user_type, $request->get('user_id'), $request->get("search"));
    }

    public function postCustomerWalletToWalletTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "amount" => "required|numeric",
            "transfer_id" => "required|numeric",
            "wallet_provider_type" => "required|in:" . $this->user_type . "," . $this->driver_type . "," ,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        return $this->userClassapi->walletToWalletTransfer($this->user_type, $request->get('user_id'), $user_check->first_name, $user_check->currency, $request->all());
    }
    public function postCountryAndCurrencyList(Request $request)
    {
        $country_list = LanguageLists::query()->select('id as country_id', 'language_name as country_name', 'language_code as country_code')->where('status', 1)->orderBy('id', 'asc')->get();
        $currency_list = WorldCurrency::query()->select('id as currency_id', 'currency_code as currency_name', 'symbol as currency_symbol')->where('status', 1)->get();
        return response()->json(array_merge([
            "status" => 1,
            //"message" => "success!",
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "country_list" => $country_list,
            "currency_list" => $currency_list
        ], \App\Helpers\AppMobileSettingsHelper::mobileBootstrapAppKeyPayload()));
    }
    public function postMyCheckoutSupportPages(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $lang_prefix = $this->adminClass->get_langugae_fields($user_check->language);
        $name = $lang_prefix != 'en_' ? $lang_prefix . 'name' : 'name';
        $description = $lang_prefix != 'en_' ? $lang_prefix . 'description' : 'description';
        $page_settings = PageSettings::query()
            ->select('id', $name, $description)
            ->where('type', 1)->get();
        if (!$page_settings->isEmpty()) {
            $support_pages = [];
            foreach ($page_settings as $page_setting) {
                $support_pages[] = [
                    'id' => $page_setting->id,
                    'page_name' => $page_setting->$name,
                    'page_title' => $user_check->language == 'en' ? (str_replace('-', " ", $page_setting->name)) : $page_setting->$name,
                    'description' => $page_setting->$description != Null ? $page_setting->$description : ''
                ];
            }
            return response()->json([
                "status" => 1,
//                "message" => "success!",
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "pages" => $support_pages
            ]);
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "data not found!",
                "message" => __('user_messages.0'),
                "message_code" => 0
            ]);
        }
    }
    public function postFirebaseSecurityRules(Request $request)
    {
        $general_settings = request()->get('general_settings');
        if ($general_settings == null || $general_settings->server_map_key == null) {
            return \App\Helpers\GoogleMapsApiResponse::missingMapKey();
        }

        $url = trim((string) $request->get('url', ''));
        if ($url === '' || ! \App\Helpers\GoogleMapsProxyHelper::isAllowedUrl($url)) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ], 400);
        }

        $server_key = (string) $general_settings->server_map_key;
        $final_url = str_replace(' ', '%20', \App\Helpers\GoogleMapsProxyHelper::appendServerKey($url, $server_key));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $final_url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError !== '') {
            return \App\Helpers\GoogleMapsApiResponse::curlFailure($curlError);
        }

        $response = json_decode((string) $result, true);

        return \App\Helpers\GoogleMapsApiResponse::proxyJson(is_array($response) ? $response : null, $httpCode > 0 ? $httpCode : 200);
    }
    public function postFacebookUserDataDeletion(Request $request)
    {
        $reference = Str::random(10);
        $domain_url = route('get:homepage');
        $status_url = $domain_url . '/deletion/' . $reference;
        $confirmation_code = $reference;
        try {
            $signed_request = $request->get('signed_request');
            $data = $this->userClassapi->parse_signed_request($signed_request, 'user');
            $user_id = $data['user_id'];

            $user = User::query()->where('login_type', 'facebook')->where('login_id', $user_id)->first();

            if ($user != Null) {
                $user->login_id = $reference;
                $user->email = Null;
                $user->contact_number = Null;
                $user->access_token = Null;
                $user->device_token = Null;
                $user->save();
            }

            $data = [
                'url' => $status_url,
                'confirmation_code' => $confirmation_code
            ];
        } catch (\Exception $e) {
            $data = [
                'url' => $status_url,
                'confirmation_code' => $confirmation_code
            ];
        }

        return response()->json($data);
    }

    /* This function sends a request to the Google Places Autocomplete API to fetch place suggestions based on a user's input */
    public function postAutocompleteGooglePlaces(Request $request)
    {
        // fetch general settings
        $general_settings = GeneralSettings::query()->select('id', 'server_map_key')->first();

        // general settings null or mapKey null then return Please try again
        if ($general_settings == Null || $general_settings->server_map_key == Null) {
            return \App\Helpers\GoogleMapsApiResponse::missingMapKey();
        }

        // Google Places Autocomplete API URL for cURL call
        $url = "https://places.googleapis.com/v1/places:autocomplete";

        /*   PAYLOAD STARTS   */
        // Prepare request payload
        $payload = [
            'input' => $request->input('input'), // type: string, The user's search query(partial address or place name or landmark)
            'locationBias' => [ // type: array, Used to influences ranking
                'circle' => [ // type: array, Specifies a circular area around a point to bias search results
                    'center' => [ // type: array, Defines central point of the location bias
                        'latitude' => $request->input('latitude'), // type: float, Defines latitude coordinate of the central point
                        'longitude' => $request->input('longitude') // type: float, Defines longitude coordinate of the central point
                    ],
                    'radius' => 50000, // type: integer, The radius around the central point(in meters)  task: Places within this area will appear higher in the search results
                    // min-radius: 0 km -> Disables location biasing. Results are ranked normally
                    // max-radius: 50 km -> Strongly biases results toward the specified location
                ]
            ]
        ];
        /*   PAYLOAD ENDS   */

        // request headers
        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key:' . $general_settings->server_map_key,
        ];

        // cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true); // post request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); // payload

        $result = curl_exec($ch);

        // Check for cURL errors
        $curl_error = curl_error($ch);
        if (!empty($curl_error)) {
            curl_close($ch);
            return \App\Helpers\GoogleMapsApiResponse::curlFailure($curl_error);
        }

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode response
        $decodedResult = json_decode($result, true);

        // Return the response from Google Places API
        return \App\Helpers\GoogleMapsApiResponse::proxyJson(is_array($decodedResult) ? $decodedResult : null, $httpStatus);
    }

    /* This function retrieves place details from the Google Places API based on a provided place_id */
    public function postGooglePlaceDetails(Request $request)
    {
        // place_id:  if search query is "kkv" and click on that search result there is unique place_id for that result (e.g: ChIJCdxbob_LWTkR8bLPvJgnjOM)
        // fetch general_settings
        $general_settings = GeneralSettings::query()->select('id', 'server_map_key')->first();
        // if general_settings Null or mapKey null
        if ($general_settings == Null || $general_settings->server_map_key == Null) {
            return \App\Helpers\GoogleMapsApiResponse::missingMapKey();
        }

        // Google Places API URL for cURL call
        $url = "https://places.googleapis.com/v1/places/" . $request->input('place_id');

        // request headers
        // X-Goog-FieldMask param used to specify which fields should be included in the API response
        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key:' . $general_settings->server_map_key,
            'X-Goog-FieldMask: displayName,formattedAddress,location,addressComponents' // four fields that are required in API response
        ];

        // cURL request, Don't require any payload
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true); // This API supports GET request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($ch);

        // Check for cURL errors
        $curl_error = curl_error($ch);
        if (!empty($curl_error)) {
            curl_close($ch);
            return \App\Helpers\GoogleMapsApiResponse::curlFailure($curl_error);
        }

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode response
        $decodedResult = json_decode($result, true);

        // Return the response from Google Places API
        return \App\Helpers\GoogleMapsApiResponse::proxyJson(is_array($decodedResult) ? $decodedResult : null, $httpStatus);
    }

    /* This function requests route details from the Google Routes API, including origin, destination, waypoints, and traffic information. */
    public function postGoogleRouteDetails(Request $request)
    {
        // fetch general_settings
        $general_settings = GeneralSettings::query()->select('id', 'server_map_key')->first();
        // if general_settings Null or mapKey Null
        if ($general_settings == Null || $general_settings->server_map_key == Null) {
            return \App\Helpers\GoogleMapsApiResponse::missingMapKey();
        }

        // Google Places API URL
        $url = "https://routes.googleapis.com/directions/v2:computeRoutes";

        // Parse waypoints from JSON string
        $waypoints = [];
        // filled method:
        // It returns true if the waypoint field is present and has a non-empty value.
        //It returns false if the waypoint is missing or empty (including null, "", or an empty array).
        if ($request->filled('waypoint')) {
            // decode request json waypoint parameter to associative array
            $decodedWaypoints = json_decode($request->input('waypoint'), true);
            if (is_array($decodedWaypoints)) {
                foreach ($decodedWaypoints as $point) {
                    $waypoints[] = [
                        "via" => true,
                        "location" => [
                            "latLng" => [
                                "latitude" => $point['latitude'],
                                "longitude" => $point['longitude']
                            ]
                        ]
                    ];
                }
            }
        }

        /*   PAYLOAD STARTS   */
        // Prepare request payload
        $payload = [
            "origin" => [ // Defines starting location of the route
                "location" => [
                    "latLng" => [
                        "latitude" => $request->input('pickup_latitude'),
                        "longitude" => $request->input('pickup_longitude')
                    ]
                ]
            ],
            "destination" => [ // Defines ending location of the route
                "location" => [
                    "latLng" => [
                        "latitude" => $request->input('drop_latitude'),
                        "longitude" => $request->input('drop_longitude')
                    ]
                ]
            ],
            // Max Limit: 25 waypoints (for standard API), 200 (for premium users).
            "intermediates" => $waypoints, // A list of waypoints (stopping points) between the origin and destination.
            "travelMode" => "DRIVE", // For cars and vehicles or Travel by passenger car.
            // "TRAFFIC_ON_POLYLINE" – Includes real-time traffic impact. "TOLLS" – Calculates toll costs on the route.
            "extraComputations" => ["TRAFFIC_ON_POLYLINE", "TOLLS"],
            // Optimized based on real-time traffic.
            "routingPreference" => "TRAFFIC_AWARE",
            // Controls the precision of the route polyline (map drawing) and  More precise, larger response.
            "polylineQuality" => "HIGH_QUALITY",
            "languageCode" => "en"
        ];
        /*   PAYLOAD ENDS   */

        // request headers
        // X-Goog-FieldMask param used to specify which fields should be included in the API response
        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key:' . $general_settings->server_map_key,
            'X-Goog-FieldMask: routes.duration,routes.distanceMeters,routes.polyline,routes.travelAdvisory,routes.legs.steps.navigationInstruction'
        ];

        // cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);

        // Check for cURL errors
        $curl_error = curl_error($ch);
        if (!empty($curl_error)) {
            curl_close($ch);
            return \App\Helpers\GoogleMapsApiResponse::curlFailure($curl_error);
        }

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode response
        $decodedResult = json_decode($result, true);

        // Return the response from Google Places API
        return \App\Helpers\GoogleMapsApiResponse::proxyJson(is_array($decodedResult) ? $decodedResult : null, $httpStatus);
    }

   //code check api splash screen data
    public function postAppVersionCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "app_type" => "required|in:0,1,2,3",
            "login_device" => "required|in:1,2,3,4",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $app_type = $request->get('app_type');;
        $login_device = $request->get('login_device');
        $general_settings = request()->get("general_settings");

        $is_google_login = ($general_settings != Null && $general_settings->is_google_login != Null) ? $general_settings->is_google_login : 0;
        $is_facebook_login = ($general_settings != Null && $general_settings->is_facebook_login != Null) ? $general_settings->is_facebook_login : 0;
        $is_apple_login = ($general_settings != Null && $general_settings->is_apple_login != Null) ? $general_settings->is_apple_login : 0;
        $is_finger_login = ($general_settings != Null && $general_settings->is_finger_login != Null) ? $general_settings->is_finger_login : 0;

        if ($app_type != "" && $login_device > 0) {
            $app_version_details = AppVersionSetting::query()
                ->where("app_type", "=", $app_type)
                ->where("app_device_type", "=", $login_device)
                //            ->where("forcefully_type", "=", 1)
                ->orderBy("id", "desc")
                ->first();
            $is_forcefully_update = 0;
            $version_name = "";

            if ($app_version_details != Null) {
                $is_forcefully_update = $app_version_details->forcefully_type - 0;
                $version_name = $app_version_details->version_name;
            }

            return response()->json(array_merge([
                "status" => 1,
                'message' => __('user_messages.1'),
                "message_code" => 1,
                "app_version" => $version_name . "",
                "is_forcefully_update" => $is_forcefully_update,
                "is_google_login" => $is_google_login,
                "is_facebook_login" => $is_facebook_login,
                "is_apple_login" => $is_apple_login,
                "is_finger_login" => $is_finger_login,
            ], \App\Helpers\AppMobileSettingsHelper::pricingAndCommissionPayload($general_settings), \App\Helpers\AppMobileSettingsHelper::mobileBootstrapAppKeyPayload()));
        } else {
            return response()->json([
                "status" => 1,
                'message' => __('user_messages.1'),
                "message_code" => 1,
                "app_version" => "",
                "is_forcefully_update" => 0,
            ]);
        }

    }

    /**
     * Regional market catalog (countries, cities, currency, min fares).
     * Optional: current_lat, current_lng OR country_id + city_id.
     */
    public function postMarketConfig(Request $request)
    {
        $lat = $request->filled('current_lat') ? (float) $request->get('current_lat') : null;
        $lng = $request->filled('current_lng') ? (float) $request->get('current_lng') : null;
        $countryId = $request->get('country_id');
        $cityId = $request->get('city_id');

        return response()->json(array_merge([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
        ], \App\Helpers\MarketConfigHelper::apiPayload($lat, $lng, $countryId, $cityId)));
    }

    //remove provider account(softdelete)
    public function postUserRemoveAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_id = $request->get('user_id');
        $user_check = $this->userClassapi->checkUserAllow($user_id, $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        RideSessionHelper::reconcileForUser((int) $user_id, $this->notificationClass);

        if (RideSessionHelper::hasBlockingRideActivity((int) $user_id)) {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.301'),
                "message_code" => 5,
            ]);
        }

        $get_provider_details = User::query()->where('id', $request->get("user_id"))->first();
        if ($get_provider_details == Null) {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.5'),
                "message_code" => 5,
            ]);
        }

        if ((int) $get_provider_details->is_driver_type === 1) {
            $pending_payment_ride = TransportRideBook::query()
                ->where('driver_id', $user_id)
                ->where('status', 9)
                ->where('driver_pay_settle_status', 0)
                ->count();
            if ($pending_payment_ride > 0) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.301'),
                    "message_code" => 5,
                ]);
            }
        }

        if ($get_provider_details->fix_user_show == 1) {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.317'),
                "message_code" => 5,
            ]);
        }

        $this->userClassapi->forfeitWalletBalanceOnAccountDeletion((int) $user_id);

        $get_provider_details->delete();

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
        ]);
    }

    public function postCustomerMassNotificationList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "page" => "nullable|numeric",
            "per_page" => "nullable|numeric"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $per_page = 10;
        if ($request->get('per_page') != Null) {
            $per_page = $request->get('per_page');
        }
        $mass_notification_list = PushNotification::query()->select('id', 'title',
            DB::raw("(CASE WHEN title != '' THEN title ELSE '' END) as title"),
            'message', DB::raw("(CASE WHEN created_at IS NOT NULL THEN created_at ELSE '' END) as datetime"));

        if ($user_check->active_mode == 1){
            $mass_notification_list = $mass_notification_list->whereIn('notification_type',[1,2]);
        }else{
            $mass_notification_list = $mass_notification_list->whereIn('notification_type',[1,3]);
        }

        $mass_notification_list = $mass_notification_list->orderBy('id', 'desc')
            ->paginate($per_page);

        $get_items_list = $mass_notification_list->items();
        $current_page = $mass_notification_list->currentPage();
        $last_page = $mass_notification_list->lastPage();
        $total = $mass_notification_list->total();
        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "current_page" => $current_page - 0,
            "last_page" => $last_page - 0,
            "total" => $total - 0,
            "mass_notification_list" => $get_items_list
        ]);

    }

    public function postUserActiveMode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "active_mode" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_id = $request->get('user_id');
        $user_check = $this->userClassapi->checkUserAllow($user_id, $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $user = User::query()->where('id',$user_id)->whereNull('deleted_at')->first();
        if($user == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        if($request->get('active_mode') == 1 && $user->is_driver_type == 1){
            $user->driver_current_status = 0;
        }
        $user->active_mode = $request->get('active_mode');
        $user->save();

        return response()->json([
           'status' => 1,
           'message' => __('user_messages.1'),
            'message_code' => 1,
           'active_mode' =>  $user->active_mode
        ]);;
    }

    public function postCustomerDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_id = $request->get('user_id');
        $user_check = $this->userClassapi->checkUserAllow($user_id, $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        $avatar_url = url('/assets/images/profile-images/customer/');
        $user = User::query()->select('first_name','email','country_code','contact_number','emergency_contact','emergency_contact_name','emergency_country_code',
            DB::raw("(CASE WHEN avatar != '' THEN (concat('$avatar_url','/',avatar,'?v=0.4')) ELSE '' END) as profile_image"))
            ->where('id',$user_id)->whereNull('deleted_at')->first();
        if($user == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
            'user_details' => $user
        ]);

    }

    public function postDriverStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_id = $request->get('user_id');
        $user_check = $this->userClassapi->checkUserAllow($user_id, $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $user = User::query()->where('id',$user_id)->whereNull('deleted_at')->first();
        if($user == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
            'is_driver_type' =>  $user->is_driver_type,
            'is_driver_status' =>  $user->is_driver_status,
            'driver_doc_status' =>  $user->driver_doc_status,
            'driver_vehicle_status' =>  $user->driver_vehicle_status,
            'reject_reason' =>  $user->rejected_reason
        ]);

    }

    public function postReferInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_details = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_details)) {
            return $failed;
        }

         = \App\Support\UserCurrencyResolver::ratioForUser($user_details);

        $avatar = url('/assets/images/profile-images/customer/');
        $refer_history = UserReferHistory::query()
            ->select(
                'users.first_name as name',
                'refer_discount_type',
                DB::raw("
            ROUND(
                CASE
                    WHEN refer_discount_type = 1
                    THEN refer_discount * $currency
                    ELSE refer_discount
                END
            , 2) AS refer_discount
        "),
                'refer_status',
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$avatar','/',users.avatar)) ELSE '' END) as profile_image")
            )
            ->join('users', 'users.id', '=', 'user_refer_history.user_id')
            ->where('user_refer_history.refer_id', $user_details->id)
            ->get()
            ->toArray();

        $user_used_refer = UserReferHistory::query()
            ->select(
                'users.first_name as name',
                'user_discount_type',
                DB::raw("
            ROUND(
                CASE
                    WHEN user_discount_type = 1
                    THEN user_discount * $currency
                    ELSE user_discount
                END
            , 2) AS user_discount
        "),
                'user_status'
            )
            ->join('users', 'users.id', '=', 'user_refer_history.refer_id')
            ->where('user_refer_history.user_id', $user_details->id)
            ->first();

        return response()->json([
            "status" => 1,
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "refer_info" => $refer_history,
            "used_info" => $user_used_refer
        ]);

    }


    //cashout  postDriverRequestCashout Module
    public function postDriverRequestCashout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required",
            "amount" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        $settings = request()->get("general_settings");

         = \App\Support\UserCurrencyResolver::ratioForUser($user_check);
        $currency_code = $user_currency != Null ? $user_currency->currency_code : '';

        $amount_to_default = round($request->get('amount')/ $currency, 2);

        if($amount_to_default < $settings->min_cashout){
            return response()->json([
                 "status" => 0,
                'message' => __('user_messages.339',['amount' => $user_currency->symbol . ' ' . round($settings->min_cashout * $currency, 3)]),
                "message_code" => 339,
            ]);
        }

        if($amount_to_default > $settings->max_cashout){
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.340',['amount'=> $user_currency->symbol . ' '.round($settings->max_cashout *  $currency,3)]),
                "message_code" => 340,
            ]);
        }
        //get wallet balance
        $last_amount = $this->notificationClass->getWalletBalance($request->get('user_id'));
        if($last_amount < $amount_to_default){
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.336'),
                "message_code" => 336
            ]);
        }

        if($request->get('amount') > 0){
            $cash_out = new CashOut();
            $cash_out->user_id = $request->get('user_id');
            $cash_out->user_name = $user_check->first_name;
            $cash_out->amount = $amount_to_default;
            $cash_out->save();

            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $request->get('user_id');
            $add_balance->wallet_provider_type = 0;
            $add_balance->transaction_type = 2;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject = "cashout";
            $add_balance->subject_code = 17;
            $add_balance->remaining_balance = floatval($last_amount - $amount_to_default);
            $add_balance->save();

            $last_amount = $add_balance->remaining_balance;
        }

        return response()->json([
            "status" => 1,
            'message' => __('user_messages.341'),
            "message_code" => 341,
            "wallet_balance" => round($last_amount * $currency, 2),
        ]);

    }

    public function postRidePricing(Request $request)
    {
        info("postRidePricing");
        info($request);
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "service_category_id" => "required|numeric",
            "distance" => "required|numeric",
            "estimate_time" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $user_currency = \App\Support\UserCurrencyResolver::forUser($user_check);
        $currency = $user_currency != null ? (float) $user_currency->ratio : 1.0;

        $vehicle_service = VehicleService::query()->where('id',$request->get('service_category_id'))->first();
        if($vehicle_service == Null){
            return response()->json([
                "status" => 1,
                'message' => __('user_messages.371'),
                "message_code" => 371,
            ]);
        }

        $distance = (float) $request->get('distance');
        $estimateTime = (float) $request->get('estimate_time');
        [$distance, $estimateTime] = \App\Support\TripMetricsSanitizer::sanitize($distance, $estimateTime);
        $costPerKm = $vehicle_service->cost_for_km;
        $timeFare = $vehicle_service->time_fare;
        $minFare = $vehicle_service->min_fare;
        $maxBargainPercent = $vehicle_service->max_bargain_percent;
        $maxOfferPercent = $vehicle_service->max_offer_percent;

// Calculate base recommended fare
        $recommendedFare = ($costPerKm * $distance) + ($timeFare * $estimateTime);

// Ensure recommended fare meets minimum fare
        $recommendedFare = max($recommendedFare, $minFare);

// Calculate min and max fare range based on bargain percent
        $bargainAmount = ($recommendedFare * $maxBargainPercent) / 100;
        $minPrice = max($recommendedFare - $bargainAmount, $minFare);
        $maxPrice = $recommendedFare + (($recommendedFare * $maxOfferPercent) / 100);
        if ($maxPrice < $minPrice) {
            $maxPrice = $minPrice + max(($minPrice * $maxOfferPercent) / 100, 500);
        }


        $general = request()->get('general_settings');

        $pricingEngine = app(\App\Services\FarePricingEngine::class);
        $priced = $pricingEngine->priceRange(
            (float) $recommendedFare,
            (float) $minPrice,
            (float) $maxPrice,
            ['datetime' => now()]
        );
        $recommendedFare = $priced['recommended'];
        $minPrice = $priced['min'];
        $maxPrice = $priced['max'];

        $recommendedOut = round($recommendedFare * $currency, 2);
        $minOut = round($minPrice * $currency, 2);
        $maxOut = round($maxPrice * $currency, 2);
        // Never surface a zero suggested fare when distance/time imply a real trip.
        if ($recommendedOut <= 0 && ($distance > 0 || $estimateTime > 0) && $minFare > 0) {
            $recommendedOut = max(round($minFare * $currency, 2), 0.01);
            $minOut = max($minOut, $recommendedOut);
            $maxOut = max($maxOut, $recommendedOut);
        }

        return response()->json(array_merge([
            "status" => 1,
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "recommended_fare" => $recommendedOut,
            "min_price" => $minOut,
            "max_price" => $maxOut,
        ], \App\Helpers\AppMobileSettingsHelper::pricingAndCommissionPayload($general, $user_currency)));
    }

    public function postCustomerAddAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->userAddressManage($request, true, false, false);
    }

    public function postCustomerEditAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->userAddressManage($request, false, true, false);
    }

    public function postCustomerDeleteAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->userAddressManage($request, false, false, true);
    }

    public function postCustomerAddressList(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        //getting user check & fetching its details
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        //getting address type wise address list
        $address_list = [];
        $addresses = UserAddress::select('id as address_id', 'address_type', 'address', 'lat_long')
            ->where('user_id', $request->get('user_id'))
            ->where('status', 1)
            ->get()
            ->groupBy('address_type');
        if (!$addresses->isEmpty()) {
            foreach ($addresses as $k => $v) {
                foreach ($v as $key => $value) {
                    $lat_long = explode(',', $value->lat_long);
                    $v[$key]->lat = isset($lat_long[0]) ? $lat_long[0] - 0 : 0;
                    $v[$key]->long = isset($lat_long[1]) ? $lat_long[1] - 0 : 0;
                }
                $address_list[] = [
                    'type' => $k,
                    'address_list' => $v
                ];
            }
        }
        return response()->json([
            "status" => 1,
//                "message" => "success!",
            'message' => __('user_messages.1'),
            "message_code" => 1,
            "type_list" => $address_list,
            "max_address_limit" => 5
        ]);

    }

    public function postUpdateDeviceToken(DeviceTokenRequest $request)
    {
        //provider_type=1-user 2-driver
        try {

            // Check provider access based on provider_type
            $provider_check = $request->provider_type == 1
                ? $this->userClassapi->checkUserAllow($request->provider_id, $request->access_token)
                : $this->userClassapi->checkDriverRegisterAllow($request->provider_id);

            if ($failed = $this->userClassapi->authJsonResponse($provider_check)) {
                return $failed;
            }

            // Manage model dynamically based on provider_type
            User::class::where('id', $request->provider_id)->update(['device_token' => $request->device_token]);
            return response()->json([
                "status" => 1,
                "message" => __('user_messages.1'),
                "message_code" => 1,
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status"       => 0,
                "message"      => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }
    }

}
