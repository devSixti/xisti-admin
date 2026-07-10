<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 18-02-2019
 * Time: 12:44 PM
 */

namespace App\Classes;

use App\Helpers\FcmPushHelper;
use App\Helpers\PushEventTemplateHelper;
use App\Helpers\VehicleCommissionHelper;
use App\Helpers\TransactionalMailHelper;
use App\Jobs\AutoMail;
use App\Jobs\FavDriverSendNotification;
use App\Jobs\NearestAlgoSendDriverNotification;
use App\Models\ApiLogDetail;
use App\Models\DriverBid;
use App\Models\DriverNewRequestNotification;
use App\Models\EmailTemplates;
use App\Models\GeneralSettings;
use App\Models\Provider;
use App\Models\ProviderServices;
use App\Models\ProviderUserRunningService;
use App\Models\ServiceCategory;
use App\Models\ServiceSettings;
use App\Models\TransportDriverDetails;
use App\Models\TransportRentalRideBooking;
use App\Models\TransportRideBook;
use App\Models\TransportRideDriverToken;
use App\Models\TransportRideFavDriverTokenList;
use App\Models\TransportVehicleType;
use App\Models\User;
use App\Models\UserReferHistory;
use App\Models\UserRideWayPoint;
use App\Models\UserRunningRide;
use App\Models\UserWalletTransaction;
use App\Models\VehicleService;
use App\Models\WorldCurrency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationClass
{
    private $transport_service_id_array;
    private $courier_service_id_array;
    private $courier_scooter;
    private $courier_car;
    private $bike_ride;
    private $taxi_ride;
    private $email_time = 5;//in seconds

    public function __construct()
    {
        $this->transport_service_id_array = [1, 2];
        $this->courier_service_id_array = [4];
        $this->courier_scooter = 10;
        $this->courier_car = 14;
        $this->bike_ride = 1;
        $this->taxi_ride = 2;
    }

    //Api Log Details Code
    public function ApiLogDetail($logger_type = Null, $logger_id = Null, $log_api_name = "", $request = [])
    {
        try {
            $remove_log_date = date('Y-m-d H:i:s', strtotime("-3 days"));
            ApiLogDetail::query()->where("created_at", "<=", $remove_log_date)->delete();
            $api_log_detail = new ApiLogDetail();
            $api_log_detail->logger_type = $logger_type;
            $api_log_detail->logger_id = $logger_id;
            $api_log_detail->log_api_name = $log_api_name;
            $api_log_detail->log_json = json_encode($request);
            $api_log_detail->save();
        } catch (\Exception $e) {
        }
    }

    public function driverAcceptedTransportRequestNotification($request_id, $driver_id)
    {
        $ride = TransportRideBook::query()->where('id', $request_id)->first();
        if ($ride != Null) {
            if ($ride->status == 1) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.23'),
                    "message_code" => 23,
                ]);
            } elseif ($ride->status == 4) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.24'),
                    "message_code" => 24,
                ]);
            } elseif ($ride->status == 10) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.25'),
                    "message_code" => 25,
                ]);
            }

            $service_settings = ServiceSettings::query()->first();
            $commissionPercent = VehicleCommissionHelper::resolvePercent(
                (int) $ride->vehicle_service_id,
                $ride->delivery_variant ?? null
            );

            $user_details = User::query()->where('id',$ride->user_id)->where('status',1)->whereNull('deleted_at')->first();
            if($user_details == Null){
                return response()->json([
                    'status' => 5,
                    'message' => __('user_messages.5'),
                    'message_code' => 5,
                ]);
            }

            $get_driver_id = TransportDriverDetails::query()
                ->select('transport_driver_details.*','users.first_name as driver_name','users.device_token','users.email as email','users.login_device','users.language')
                ->join('users','users.id','=','transport_driver_details.user_id')
                ->where('transport_driver_details.user_id',$driver_id)->first();

            if ($get_driver_id == Null) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }

            $get_vehicle_type = TransportVehicleType::query()->where('id',$get_driver_id->vehicle_type_id)->first();
            if($get_vehicle_type == NULL){
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }


            if ($get_driver_id != Null) {
                    $user = User::query()->where('id', $ride->user_id)->first();
                    if ($user == Null) {
                        return response()->json([
                            "status" => 0,
//                            "message" => "user not found",
                            "message" => __('driver_messages.9'),
                            "message_code" => 9,
                        ]);
                    }
                    if ($ride->ride_type == 0) {
                        $ride->status = 1;
                    } else {
                        $ride->status = 2;
                    }

                    $driver_bid = DriverBid::query()->where('ride_id',$ride->id)->where('driver_id',$driver_id)->where('status',0)->first();
                    if($driver_bid == Null){
                        return response()->json([
                            "status" => 0,
                            "message" => __('user_messages.326'),
                            "message_code" => 326,
                        ]);
                    }
                    $driver_bid->status = 1;
                    $driver_bid->save();

                    if ((int) $ride->vehicle_service_id !== 4) {
                        $ride->vehicle_service_id = $get_vehicle_type->service_id;
                    }
                    $ride->vehicle_type_id = $get_driver_id->vehicle_type_id;
                    $ride->driver_name = $get_driver_id->driver_name;
                    $ride->driver_id = $driver_id;
                    $ride->offered_price = $driver_bid->offered_price;
                    $ride->total_pay = $driver_bid->offered_price;
                    $ride->admin_commission = round((($driver_bid->offered_price * $commissionPercent) / 100), 2);
                    $ride->driver_amount = $driver_bid->offered_price - round((($driver_bid->offered_price * $commissionPercent) / 100), 2);
                    $ride->save();

                if ($user_details->pending_refer_discount > 0) {
                    $user = User::query()->where('id', $user_details->id)->whereNull('deleted_at')->first();
                    if ($user != Null) {
                        $user_refer_history = UserReferHistory::query()->where('user_id', $user_details->id)->where('user_status', 0)->first();
                        $total_price = $ride->total_pay;
                        if ($user_refer_history != Null) {
                            if ($user_refer_history->user_discount_type == 1) {
                                $refer_discount_price = round($total_price - $user_refer_history->user_discount, 2);
                                $get_refer_discount_price = $user_refer_history->user_discount;
                            } else {
                                $refer_discount_price = round((($total_price * $user_refer_history->user_discount) / 100), 2);
                                $get_refer_discount_price = $refer_discount_price;
                                $refer_discount_price = round($total_price - $refer_discount_price, 2);
                            }
                            if ($refer_discount_price < 0) {
                                $refer_discount_price = 0;
                            }
                            $ride->total_pay = $refer_discount_price;
                            $ride->user_refer_history_id = $user_refer_history->id;
                            $ride->refer_discount = round($get_refer_discount_price, 2);
                            $ride->admin_commission = $ride->admin_commission - round($get_refer_discount_price, 2);
                            $ride->save();
                            $user_refer_history->user_status = 1;
                            $user_refer_history->save();
                            $user->pending_refer_discount = $user->pending_refer_discount - 1;
                            $user->save();
                        } else {
                            $user_refer_history = UserReferHistory::query()->where('refer_id', $user_details->id)->where('refer_status', 0)->first();
                            if ($user_refer_history != Null) {
                                if ($user_refer_history->refer_discount_type == 1) {
                                    $refer_discount_price = round($total_price - $user_refer_history->refer_discount, 2);
                                    $get_refer_discount_price = $user_refer_history->refer_discount;
                                } else {
                                    $refer_discount_price = round((($total_price * $user_refer_history->refer_discount) / 100), 2);
                                    $get_refer_discount_price = $refer_discount_price;
                                    $refer_discount_price = round($total_price - $refer_discount_price, 2);
                                }
                                if ($refer_discount_price < 0) {
                                    $refer_discount_price = 0;
                                }
                                $ride->total_pay = $refer_discount_price;
                                $ride->refer_discount = round($get_refer_discount_price, 2);
                                $ride->admin_commission = $ride->admin_commission - round($get_refer_discount_price, 2);
                                $ride->user_refer_history_id = $user_refer_history->id;
                                $ride->save();
                                $user_refer_history->refer_status = 1;
                                $user_refer_history->save();
                                $user->pending_refer_discount = $user->pending_refer_discount - 1;
                                $user->save();
                            }
                        }
                    }
                }

                    if ($ride->ride_type == 0) {
                        ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                        $provider_running_service = new ProviderUserRunningService();
                        $provider_running_service->provider_id = $driver_id;
                        $provider_running_service->user_id = $ride->user_id;
                        $provider_running_service->booking_id = $ride->id;
                        $provider_running_service->save();

                    }


                    $this->DriverTransportNotification($ride->id, $get_driver_id->device_token, $ride->status, $get_driver_id->login_device, $get_driver_id->language);
                    $notification_log = null;
                    if ($user->device_token) {
                        $notification_log = $this->userTransportNotification(
                            $ride->id,
                            $user->device_token,
                            $ride->status,
                            $user->login_device,
                            $user->language
                        );
                    }

                    if ($user->avatar != Null) {
                        if (filter_var($user->avatar, FILTER_VALIDATE_URL) == true) {
                            $avatar = $user->avatar;
                        } else {
                            $avatar = url('/assets/images/profile-images/customer/' . $user->avatar);
                        }
                    } else {
                        $avatar = "";
                    }
                    $ride_details_arr = [
                        "ride_id" => $ride->id,
                        "booking_no" => $ride->ride_no,
                        "total_amount" => round($ride->total_pay , 2),
                        "total_amount" => round($ride->total_pay , 2),
                        "ride_status" => $ride->status,
                    ];

                    $general_settings = request()->get("general_settings");
                    if ($general_settings != Null) {
                        if ($general_settings->send_mail == 1) {
                                $user_name = ucwords($ride->user_name);
                                $driver_name = ucwords($get_driver_id->driver_name);
                                $pickup_address = $ride->pickup_address;
                                $destination_address = $ride->destination_address;
                                $pickup_datetime = date("Y-m-d h:i:s", strtotime($ride->pickup_datetime));

                                //sending mail driver
                                try {
                                    $mail_type = "driver_new_ride_request_–_transport";
                                    $to_mail = $get_driver_id->email;
                                    $subject = "You have a new " . $general_settings->mail_site_name . "Ride Request";
                                    $disp_data = array("##driver_name##" => $driver_name,
                                        "##pickup_location##" => $pickup_address, "##destination_location##" => $destination_address,
                                        "##pickup_time_location##" => $pickup_datetime, "##user_name##" => $user_name);
                                    $mail_return_data = $this->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                } catch (\Exception $e) {
                                }

                                if ($general_settings->send_receive_email != Null) {
                                    //send mail to admin
                                    try {
                                        $mail_type = "admin_new_driver_received_ride_request_-_transport";
                                        $to_mail = $general_settings->send_receive_email;
                                        $subject = $driver_name . " driver get a new ride request";
                                        $disp_data = array("##driver_name##" => $driver_name,
                                            "##pickup_location##" => $pickup_address, "##destination_location##" => $destination_address,
                                            "##pickup_time_location##" => $pickup_datetime, "##user_name##" => $user_name);
                                        $mail_return_data = $this->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                    } catch (\Exception $e) {
                                    }
                                }

                            try{
                                $mail_type = "new_order_placed-transport";
                                $to_mail = $user_details->email;
                                $subject = "Your ".$general_settings->mail_site_name." Ride Request Placed";
                                $disp_data = array("##user_name##"=>$user_name);
                                $mail_return_data = $this->sendMail($subject,$to_mail,$mail_type,$disp_data);
                            } catch (\Exception $e){}
                            try{
                                if ($general_settings->send_receive_email != Null) {
                                    $pickup_datetime =  $ride->pickup_datetime;
                                    $destination_address = $ride->destination_address;
                                    $pickup_address =  $ride->pickup_address;
                                    $driver_name = "";
                                    $mail_type = "admin_new__ride_request_-_transport";
                                    $to_mail = $general_settings->send_receive_email;
                                    $subject = $driver_name."  get a new ride request" ;
                                    $disp_data = array("##driver_name##"=>$driver_name,
                                        "##pickup_location##"=>$pickup_address,"##destination_location##"=>$destination_address,
                                        "##pickup_time_location##"=>$pickup_datetime,"##user_name##"=>$user_name);
                                    $mail_return_data = $this->sendMail($subject,$to_mail,$mail_type,$disp_data);
                                }
                            } catch (\Exception $e){}
                        }
                    }
                    return response()->json([
                        "status" => 1,
                        //"message" => "success!",
                        "message" => __('driver_messages.1'),
                        "message_code" => 1,
                        "ride_details" => $ride_details_arr,
                        "noti_log" => $notification_log
                    ]);
            } else {
                return response()->json([
                    "status" => 0,
                    //"message" => "something went to wrong!",
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "ride not found!",
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }
    }
    public function driverCancelNotification($request_id, $token, $user_id, $login_device, $language, $cancel_by = Null)
    {
        if (trim((string) $token) === '') {
            return null;
        }

        $ride = TransportRideBook::query()->where('id', $request_id)->first();
        if ($ride == null) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.26'),
                'message_code' => 9,
            ]);
        }

        $language = $language != null ? $language : 'es';

        return $this->sendEventPushToToken(
            'driver_passenger_cancelled',
            $token,
            $language,
            [],
            [
                'user_type' => '2',
                'ride_id' => (string) $request_id,
                'ride_status' => (string) $ride->status,
                'ride_type' => (string) $ride->ride_type,
            ],
            (int) $login_device,
        );
    }

    public function driverCompletedNotification($token, $login_device, $language, $ride_id, $status)
    {
        if (trim((string) $token) === '') {
            return null;
        }

        $ride = $ride_id > 0 ? TransportRideBook::query()->where('id', $ride_id)->first() : null;
        if ($ride_id > 0 && $ride == null) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.9'),
                'message_code' => 9,
            ]);
        }

        $language = $language != null ? $language : 'es';

        return $this->sendEventPushToToken(
            'driver_ride_completed',
            $token,
            $language,
            [],
            [
                'user_type' => '2',
                'ride_id' => (string) $ride_id,
                'ride_status' => (string) $status,
                'ride_type' => (string) ($ride->ride_type ?? 0),
            ],
            (int) $login_device,
        );
    }

    //Driver notification
    public function DriverTransportNotification($ride_id, $device_token, $ride_status, $login_device, $language = Null)
    {
        $language = $language != Null ? $language : 'en';
        if ($ride_id == Null || $device_token == Null || $ride_status == Null) {
            return response()->json([
                'status' => 0,
//                'message' => "Something want wrong!",
                'message' => __('driver_messages.9'),
                'message_code' => 9
            ]);
        }

        $ride = TransportRideBook::query()->where('id', $ride_id)->first();
        if ($ride == Null) {
            return response()->json([
                'status' => 0,
//                'message' => "Something want wrong!",
                'message' => __('driver_messages.9'),
                'message_code' => 9
            ]);
        }

        $eventKey = $this->driverRideEventKey((int) $ride_status, $ride);
        if ($eventKey === null) {
            return response()->json(['status' => 1, 'message' => __('user_messages.1'), 'message_code' => 1]);
        }
        $event = $this->applyEventTemplate($eventKey, $language);
        $title = $event['title'];
        $message = $event['message'];
        $title_code = $event['title_code'];
        $message_code = $event['message_code'];
        //notification type 0= simple , 1= communication
            $extraNotificationData = [
                'title' => $title."",
                'title_code' => $title_code."",
                'sound' => "true",
                'notification_type' => (string) $event['notification_type'],
                'ride_id' => $ride_id."",
                'ride_status' => $ride_status."",
                'message' => $message."",
                'body' => $message."",
                'message_code' => $message_code."",
                'ride_type' => $ride->ride_type."",
                'user_type' => "2",
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ];
        if ($title !== '' || $message !== '') {
            FcmPushHelper::sendToTokenForLoginDevice($device_token, $title, $message, $extraNotificationData, $event['sound'], (int) $login_device);
        }

        return response()->json(['status' => 1, 'message' => __('user_messages.1'), 'message_code' => 1]);
    }

    //user notification
    public function userTransportNotification($ride_id, $device_token, $ride_status, $login_device, $language = Null)
    {
        $language = $language != Null ? $language : 'en';

        $completed_by = 0;
        if ($ride_id == Null || $device_token == Null || $ride_status == Null) {
            return response()->json([
                'status' => 0,
//                'message' => "Something want wrong!",
                'message' => __('driver_messages.9'),
                'message_code' => 9
            ]);
        }

        $ride = TransportRideBook::query()->where('id', $ride_id)->first();
        if ($ride == Null) {
            return response()->json([
                'status' => 0,
//                'message' => "Something want wrong!",
                'message' => __('driver_messages.9'),
                'message_code' => 9
            ]);
        }
        $completed_by = $ride->completed_by;
        $eventKey = $this->passengerRideEventKey((int) $ride_status, $ride, (int) $completed_by);
        if ($eventKey === null) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.9'),
                'message_code' => 9
            ]);
        }
        $event = $this->applyEventTemplate($eventKey, $language);
        $title = $event['title'];
        $message = $event['message'];
        $title_code = $event['title_code'];
        $message_code = $event['message_code'];
            $extraNotificationData = [
                'title' => $title."",
                'title_code' => $title_code."",
                'sound' => "true",
                'notification_type' => (string) $event['notification_type'],
                'user_type' => "1",
                'ride_id' => $ride_id."",
                'ride_status' => $ride_status."",
                'message' => $message."",
                'body' => $message."",
                'message_code' => $message_code."",
                'ride_type' => $ride->ride_type."",
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ];
        if ($title !== '' || $message !== '') {
            FcmPushHelper::sendToTokenForLoginDevice($device_token, $title, $message, $extraNotificationData, $event['sound'], (int) $login_device);
        }

        return response()->json(['status' => 1, 'message' => __('user_messages.1'), 'message_code' => 1]);
    }

    public function dateLangConvert($date, $lang = "en")
    {
        if ($lang == "en") {
            return $date;
        } else {
            $search = config('dateconstants.en');
            $replace = config('dateconstants.' . $lang);
            if ($replace == Null) {
                return $date;
            }
            $new_date = str_ireplace($search, $replace, $date);
            return $new_date;
        }
    }


    public function userWalletTransferNotification($title, $message, $transfer_wallet_holder_device_token = "", $transfer_wallet_holder_login_device = "", $amount_to_default = 0)
    {
        if ($transfer_wallet_holder_device_token == "" || $transfer_wallet_holder_login_device == "") {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ]);
        }

        return $this->sendEventPushToToken(
            'passenger_wallet_update',
            $transfer_wallet_holder_device_token,
            'es',
            [],
            [],
            (int) $transfer_wallet_holder_login_device
        );
    }

    public function sendMail($subject, $to_mail, $mail_type = "", $data = [])
    {
        try {
            $general_setting = request()->get("general_settings");

            $template = EmailTemplates::query()->where('type', $mail_type)->where('status', 1)->first();
            if ($general_setting != Null && $template != Null) {
                if ($general_setting->send_mail == 1) {
                    $smtp_user_name = ($general_setting->smtp_user_name != Null) ? $general_setting->smtp_user_name : "";
                    $smtp_password = ($general_setting->smtp_password != Null) ? $general_setting->smtp_password : "";
                    $smtp_hostname = ($general_setting->smtp_hostname != Null) ? $general_setting->smtp_hostname : "";
                    $smtp_port = ($general_setting->smtp_port != Null) ? $general_setting->smtp_port : "";
                    $smtp_encryption = ($general_setting->smtp_encryption != Null) ? $general_setting->smtp_encryption : "";
                    if (TransactionalMailHelper::transportConfigured(
                        $smtp_user_name,
                        $smtp_password,
                        $smtp_hostname,
                        $smtp_port,
                        $smtp_encryption
                    )) {
                        $site_logo = asset("/assets/images/email-temp-images/" . $general_setting->website_logo);
                        $mail_logo = asset("/assets/images/email-temp-images/e-temp-email.png");
                        $fb_logo = asset("/assets/images/email-temp-images/e-temp-facebook.png");
                        $twitter_logo = asset("/assets/images/email-temp-images/e-temp-twitter.png");
                        $web_logo = asset("/assets/images/email-temp-images/e-temp-world-wide-web.png");

                        $site_email = ($general_setting->email != "") ? $general_setting->email : "#";
                        $twitter_link = ($general_setting->twitter_link != "") ? $general_setting->twitter_link : "#";
                        $facebook_link = ($general_setting->facebook_link != "") ? $general_setting->facebook_link : "#";
                        $mail_site_name = ($general_setting->mail_site_name != "") ? $general_setting->mail_site_name : "";
                        $site_url = ($general_setting->site_url != "") ? $general_setting->site_url : request()->getHost();

                        $disp_data = $data;

                        $general_data = array(
                            "##logo##" => $site_logo,
                            "##mail_site_name##" => $mail_site_name,
                            "##site_url##" => $site_url,
                            "##site_email##" => $site_email,
                            "##twitter_link##" => $twitter_link,
                            "##facebook_link##" => $facebook_link,
                            "##mail_logo##" => $mail_logo,
                            "##fb_logo##" => $fb_logo,
                            "##twitter_logo##" => $twitter_logo,
                            "##web_logo##" => $web_logo,
                        );
                        $final_data = array_merge($disp_data, $general_data);
                        $template_content = str_replace(array_keys($final_data), $final_data, $template->content);

                        $email = $to_mail;

                        $data = [
                            "type" => 3,//store
                            "path" => "mail_template.temp",
                            "email" => $email,
                            "subject" => $subject,
                            "mail_site_name" => $mail_site_name,
                            "template_content" => $template_content,
                            "smtp_user_name" => $smtp_user_name,
                            "smtp_password" => $smtp_password,
                            "smtp_hostname" => $smtp_hostname,
                            "smtp_port" => $smtp_port,
                            "smtp_encryption" => $smtp_encryption,
                        ];
                        $emailJob = (new AutoMail($data))->delay(now()->addSeconds($this->email_time));
                        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($emailJob);
                    }
                    return "";
                }
                return "";
            }
            return "";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function RequestAllDrivers($ride_id,$current_lat,$current_long,$service_id,$pickup_address,$destination_address,$offered_price,$handicap=0,$child_seat=0)
    {
        $default_currency = WorldCurrency::query()->where('default_currency',1)->first();
        $currency = $default_currency->symbol;
        $currencyRatio = (float) ($default_currency->ratio ?? 1);
        $displayOfferedPrice = round((float) $offered_price * $currencyRatio, 2);

        $service_setting = ServiceSettings::query()->first();
        if ($service_setting != Null) {
            $radius = $service_setting->provider_search_radius;
        }else {
            $radius = 5;
        }

        $vehicle_service = VehicleService::query()->where('id',$service_id)->first();
        if($vehicle_service == NULL){
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.343'),
                'message_code' => 343,
            ]);
        }

        $ride = TransportRideBook::query()->where('id', $ride_id)->first();
        $rideVariant = $ride != null
            ? \App\Helpers\XistiVehicleVariantHelper::normalize($ride->delivery_variant ?? '')
            : '';

        $isDeliveryRide = \App\Helpers\RideKindHelper::isDeliveryRide([
            'service_id' => $service_id,
            'item_description' => '',
            'recipient_name' => '',
        ]) || (($vehicle_service->service_mode ?? 'transport') === 'delivery');

        $not_multi_delivery_provider_id = ProviderUserRunningService::query()->get()->pluck('provider_id');

        $get_drivers = User::query()
            ->select('users.device_token','users.login_device','transport_driver_details.search_distance_filter','transport_driver_details.current_lat','transport_driver_details.current_long',
                DB::raw("(CASE WHEN transport_driver_details.search_distance_filter != 0 THEN ROUND((6371 * acos( cos( radians(" .$current_lat. ") ) * cos( radians(current_lat) )  * cos( radians( current_long ) - radians(" .$current_long. ") ) + sin( radians(" .$current_lat. ") ) * sin(radians( current_lat ) ) ) ), 2) ELSE 0 END) as distance")
    )
            ->join('transport_driver_details','transport_driver_details.user_id','=','users.id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
            ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
            ->whereRaw(DB::raw("(6371 * acos( cos( radians(current_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(current_long) ) + sin( radians(current_lat) ) * sin(radians( " .$current_lat. " ) ) ) ) < " . $radius));
        if ($isDeliveryRide) {
            \App\Helpers\ServiceCatalogHelper::applyDeliveryCapableDriverFilter($get_drivers);
            $requestedVehicleServiceId = null;
            if (\Illuminate\Support\Facades\Schema::hasColumn('user_courier_service_details', 'requested_vehicle_service_id')) {
                $requestedVehicleServiceId = \App\Models\TransportCourierDetails::query()
                    ->where('ride_id', $ride_id)
                    ->value('requested_vehicle_service_id');
                $requestedVehicleServiceId = $requestedVehicleServiceId !== null ? (int) $requestedVehicleServiceId : null;
            }
            if (\App\Helpers\DeliveryVehicleHelper::isValidRequestedVehicleServiceId($requestedVehicleServiceId)) {
                $get_drivers = $get_drivers->where('transport_vehicle_type.service_id', $requestedVehicleServiceId);
            }
            if ($rideVariant !== '') {
                \App\Helpers\XistiVehicleVariantHelper::applyTransportVariantDriverFilter(
                    $get_drivers,
                    $rideVariant,
                    'transport_driver_details',
                    true
                );
            }
        } else {
            $get_drivers = $get_drivers->where('vehicle_services.id', $service_id);
            \App\Helpers\XistiVehicleVariantHelper::applyTransportVariantDriverFilter($get_drivers, $rideVariant);
        }
        $get_drivers = $get_drivers
            ->where('users.driver_current_status',1)
            ->where('users.is_driver_type',1)
            ->where('users.is_driver_status',1)
            ->where('users.status',1)
            ->when($handicap == 1, function ($q) {
                $q->where('transport_driver_details.handicap', 1);
            })
            ->when($child_seat == 1, function ($q) {
                $q->where('transport_driver_details.child_seat', 1);
            })
            ->whereNotIn('users.id', $not_multi_delivery_provider_id)
            ->whereNull('users.deleted_at')
            ->havingRaw('(transport_driver_details.search_distance_filter = 0 OR distance <= transport_driver_details.search_distance_filter)')
            ->get()
            ->toArray();

        $ride = TransportRideBook::query()->where('id', $ride_id)->first();
        $rideType = $ride != null ? (string) $ride->ride_type : '0';
        $event = $this->applyEventTemplate('driver_new_request', 'es', [
            'currency' => $currency,
            'price' => (string) $displayOfferedPrice,
            'pickup' => $pickup_address,
            'destination' => $destination_address,
        ]);
        $title = $event['title'];
        $message = $event['message'];
        $title_code = $event['title_code'];
        $message_code = $event['message_code'];
        $iosSound = $event['sound'];

        //notification type 0= simple , 1= communication
        $extraNotificationData = [
            'title' => $title."",
            'title_code' => $title_code."",
            'sound' => "true",
            'notification_type' => (string) $event['notification_type'],
            'user_type' => '2',
            'ride_status' => '0',
            'ride_type' => $rideType,
            'message' => $message."",
            'body' => $message."",
            'message_code' => $message_code."",
            'ride_id' => $ride_id."",
            'service_id' => $service_id."",
            'is_delivery' => $isDeliveryRide ? '1' : '0',
            'pickup_address' => $pickup_address."",
            'destination_address' => $destination_address."",
            'offered_price' => $displayOfferedPrice."",
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            'dispatch_action' => 'refresh_available_rides',
            'dispatch_ts' => (string) time(),
        ];

        if ($title !== '' || $message !== '') {
            foreach ($get_drivers as $driver) {
                $token = trim((string) ($driver['device_token'] ?? ''));
                if ($token === '') {
                    continue;
                }
                FcmPushHelper::sendToTokenForLoginDevice(
                    $token,
                    $title,
                    $message,
                    $extraNotificationData,
                    $iosSound,
                    (int) ($driver['login_device'] ?? 0),
                );
            }
        }

        return response()->json(['status' => 1, 'message' => __('user_messages.1'), 'message_code' => 1]);
    }

    public function userBidNotification($ride_details, $device_token, $language)
    {
        if ($device_token === null || trim((string) $device_token) === '') {
            return null;
        }

        $event = $this->applyEventTemplate('passenger_driver_bid', $language ?: 'es');
        $title = $event['title'];
        $title_code = $event['title_code'];
        $message = $event['message'];
        $message_code = $event['message_code'];

        $destination = array_map('trim', explode(",", $ride_details->destination_latlong));
        $way_points[] = [
            "address" => $ride_details->pickup_address,
            "address_lat" => trim($ride_details->pickup_lat),
            "address_long" => trim($ride_details->pickup_long)
        ];

        if ($ride_details->is_way_point == 1) {
            $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride_details->id)->first();
            if ($ride_way_point != Null) {
                if ($ride_way_point->way_point_1 != Null && $ride_way_point->lat_long_1 != Null) {
                    $lat_long_1 = array_map('trim', explode(",", $ride_way_point->lat_long_1));
                    $way_points[] = [
                        "address" => $ride_way_point->way_point_1,
                        "address_lat" => trim($lat_long_1[0]),
                        "address_long" => trim($lat_long_1[1])
                    ];
                }
                if ($ride_way_point->way_point_2 != Null && $ride_way_point->lat_long_2 != Null) {
                    $lat_long_2 = explode(",", $ride_way_point->lat_long_2);
                    $way_points[] = [
                        "address" => $ride_way_point->way_point_2,
                        "address_lat" => trim($lat_long_2[0]),
                        "address_long" => trim($lat_long_2[1])
                    ];
                }
                if ($ride_way_point->way_point_3 != Null && $ride_way_point->lat_long_3 != Null) {
                    $lat_long_3 = explode(",", $ride_way_point->lat_long_3);
                    $way_points[] = [
                        "address" => $ride_way_point->way_point_3,
                        "address_lat" => trim($lat_long_3[0]),
                        "address_long" => trim($lat_long_3[1])
                    ];
                }
            }
        }

        $way_points[] = [
            "address" => $ride_details->destination_address,
            "address_lat" => trim($destination[0]),
            "address_long" => trim($destination[1])
        ];

        return $this->sendEventPushToToken(
            'passenger_driver_bid',
            $device_token,
            $language ?: 'es',
            [],
            [
                'notification_type' => '8',
                'user_type' => '1',
                'ride_id' => (string) $ride_details->id,
                'service_id' => (string) $ride_details->vehicle_service_id,
                'address_list' => json_encode($way_points),
                'offered_price' => (string) $ride_details->offered_price,
                'min_bargain_amt' => (string) $ride_details->min_bargain_amt,
                'max_bargain_amt' => (string) $ride_details->max_bargain_amt,
                'recipient_name' => isset($ride_details->recipient_name) ? (string) $ride_details->recipient_name : '',
                'recipient_contact_number' => isset($ride_details->recipient_contact_number) ? (string) $ride_details->recipient_contact_number : '',
                'item_description' => isset($ride_details->item_description) ? (string) $ride_details->item_description : '',
                'estimate_price' => isset($ride_details->estimate_price) ? (string) $ride_details->estimate_price : '0',
                'ride_type' => (string) $ride_details->ride_type,
                'dispatch_action' => 'refresh_driver_bids',
                'dispatch_ts' => (string) time(),
            ]
        );
    }

    public function userFareChangeNotification($ride_id)
    {
        $ride = TransportRideBook::query()->where('id',$ride_id)->first();
        if($ride == NULL){
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.26'),
                'message_code' => 9
            ]);
        }
        $bidding_drivers = DriverBid::query()->where('ride_id',$ride_id)->get()->pluck('driver_id');
        $not_multi_delivery_provider_id = ProviderUserRunningService::query()->get()->pluck('provider_id');
        $provider_token = User::query()
            ->select('users.device_token')
            ->join('transport_driver_details','transport_driver_details.user_id','=','users.id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
            ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
            ->where('users.driver_current_status',1)
            ->where('users.is_driver_type',1)
            ->where('users.is_driver_status',1)
            ->where('users.status',1)
            ->whereIn('users.id',$bidding_drivers)
            ->whereNotIn('users.id', $not_multi_delivery_provider_id)
            ->whereNull('users.deleted_at')
            ->get()
            ->pluck('device_token');

        return $this->sendEventPushToTokens(
            'driver_fare_changed_by_user',
            $provider_token->filter()->values()->all(),
            'es',
            [],
            [
                'ride_id' => (string) $ride_id,
                'ride_status' => (string) ($ride->status ?? 0),
                'ride_type' => (string) ($ride->ride_type ?? 0),
                'customer_name' => (string) $ride->user_name,
                'dispatch_action' => 'refresh_available_rides',
                'dispatch_ts' => (string) time(),
            ]
        );
    }

    public function sendReportIssueResolvedNotification(int $reportId, string $deviceToken, string $referenceNo, ?string $language = 'es'): mixed
    {
        if (trim($deviceToken) === '') {
            return null;
        }
        $language = $language ?: 'es';
        $title = __('user_messages.374', [], $language);
        $message = __('user_messages.375', ['value' => $referenceNo], $language);

        return FcmPushHelper::sendToToken($deviceToken, $title, $message, [
            'title' => $title,
            'body' => $message,
            'message' => $message,
            'title_code' => '346',
            'message_code' => '347',
            'sound' => 'true',
            'notification_type' => '12',
            'report_id' => (string) $reportId,
            'user_type' => '1',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], 'default');
    }

    /**
     * Report-issue chat message from admin (no notification_type — mobile opens chat UI).
     */
    public function sendReportIssueChatNotification(
        string $deviceToken,
        string $reportId,
        string $orderChatNumber,
        string $senderTitle,
        string $messageText,
        bool $isImage = false
    ): mixed {
        if (trim($deviceToken) === '') {
            return null;
        }

        $body = $isImage ? 'Image' : $messageText;
        $title = $senderTitle !== '' ? $senderTitle : 'Admin';

        return FcmPushHelper::sendToToken($deviceToken, $title, $body, [
            'sound' => 'true',
            'is_report_chat' => '1',
            'user_id' => 'a_1',
            'issue_id' => $reportId,
            'order_chat_number' => $orderChatNumber,
            'title' => $title,
            'desc' => $body,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], 'default');
    }

    //send push notification via cURL call to FCM V1 API
    public function sendPushNotification($topic,$title,$message,$notification_type)
    {
        return FcmPushHelper::sendToTopic($topic, $title, $message, [
            'notification_type' => (string) $notification_type,
        ]);
    }

    public function providerUpdateWalletBalance($provider_id,$wallet_provider_type,$transaction_type,$add_update_wallet_bal,$subject,$subject_code,$order_no){

        try{
            if(round($add_update_wallet_bal,2) > 0){
                $provider_wallet_balance = UserWalletTransaction::query()->where('user_id', $provider_id)->where('wallet_provider_type','=',$wallet_provider_type)->orderBy('id', 'desc')->first();
                if ($provider_wallet_balance != Null) {
                    $provider_balance = $provider_wallet_balance->remaining_balance;
                } else {
                    $provider_balance = 0;
                }

                $add_balance = new UserWalletTransaction();
                $add_balance->user_id = $provider_id;
                $add_balance->wallet_provider_type = $wallet_provider_type;
                $add_balance->transaction_type = $transaction_type;
                $add_balance->amount = $add_update_wallet_bal;
                //$add_balance->subject = "Paid To Ride - Order #" . $ride_details->ride_no;
                $add_balance->subject = $subject;
                if($transaction_type == 1)
                {
                    $add_balance->remaining_balance = round($provider_balance + $add_update_wallet_bal, 2);
                }else{
                    $add_balance->remaining_balance = round($provider_balance - $add_update_wallet_bal, 2);
                }
                $add_balance->subject_code = $subject_code;
                $add_balance->order_no = $order_no;
                $add_balance->save();
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    //cash out Notification
    public function DriverCashOutNotification($device_token,$language,$request_for,$login_device = null)
    {
        if ($device_token == Null) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.9'),
                'message_code' => 9,
            ]);
        }

        $language = $language != Null ? $language : 'es';
        $eventKey = (int) $request_for === 1 ? 'driver_cash_out_processed' : 'driver_cash_out_rejected';

        return $this->sendEventPushToToken($eventKey, $device_token, $language, [], [], $login_device);
    }

    //get walletBalance
    public function getWalletBalance($user_id)
    {
        $get_last_transaction = UserWalletTransaction::query()
            ->where('wallet_provider_type', "=", 0)
            ->where('user_id', "=", $user_id)
            ->orderBy('id', 'desc')
            ->first();
        if ($get_last_transaction != Null) {
            $last_amount = $get_last_transaction->remaining_balance;
        } else {
            $last_amount = 0;
        }
        return $last_amount;
    }

    //timezone
    public function getDefaultTimeZone($user_time_zone) {
        $user_time_zone = trim($user_time_zone);
        $timezoneMapping = [
            'Asia/Calcutta' => 'Asia/Kolkata',
            'Asia/Saigon' => 'Asia/Ho_Chi_Minh'
        ];

        if (array_key_exists($user_time_zone, $timezoneMapping)) {
            $user_time_zone = $timezoneMapping[$user_time_zone];
        }
        return $user_time_zone;
    }

    public function sendExpiryNotification($device_token, $warning_days = 0)
    {
        $vars = ['days' => (string) $warning_days];

        if (is_array($device_token)) {
            $recipients = FcmPushHelper::normalizeRecipients($device_token);
            if ($recipients === []) {
                return null;
            }

            foreach ($recipients as $recipient) {
                $this->sendEventPushToToken(
                    'driver_document_expiry',
                    $recipient['token'],
                    'es',
                    $vars,
                    [],
                    $recipient['login_device']
                );
            }

            return response()->json(['status' => 1, 'message' => __('user_messages.1'), 'message_code' => 1]);
        }

        return $this->sendEventPushToToken(
            'driver_document_expiry',
            $device_token,
            'es',
            $vars,
            [],
            null
        );
    }

    public function driverDirectAcceptedTransportRequestNotification($request_id, $driver_id)
    {
        $ride = TransportRideBook::query()->where('id', $request_id)->first();
        if ($ride != Null) {
            $user = User::query()->where('id', $ride->user_id)->first();
            if ($user == Null) {
                return response()->json([
                    "status" => 0,
//                            "message" => "user not found",
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }
            if ($ride->status == 1) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.23'),
                    "message_code" => 23,
                ]);
            } elseif ($ride->status == 4) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.24'),
                    "message_code" => 24,
                ]);
            } elseif ($ride->status == 10) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.25'),
                    "message_code" => 25,
                ]);
            }

            $service_settings = ServiceSettings::query()->first();
            $commissionPercent = VehicleCommissionHelper::resolvePercent(
                (int) $ride->vehicle_service_id,
                $ride->delivery_variant ?? null
            );

            $driver_detail = TransportDriverDetails::query()
                ->select('transport_driver_details.*','users.first_name as driver_name','users.device_token','users.login_device','users.language','users.id as user_id')
                ->join('users','users.id','=','transport_driver_details.user_id')
                ->where('transport_driver_details.user_id',$driver_id)->first();

            if ($driver_detail == Null) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }

            $get_vehicle_type = TransportVehicleType::query()->where('id',$driver_detail->vehicle_type_id)->first();
            if($get_vehicle_type == NULL){
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }

            if ($driver_detail != Null) {
                if ($ride->ride_type == 0) {
                    $ride->status = 1;
                } else {
                    $ride->status = 2;
                }

                if ((int) $ride->vehicle_service_id !== 4) {
                    $ride->vehicle_service_id = $get_vehicle_type->service_id;
                }
                $ride->vehicle_type_id = $driver_detail->vehicle_type_id;
                $ride->driver_name = $driver_detail->driver_name;
                $ride->driver_id = $driver_id;
                $ride->admin_commission = round(($ride->total_pay * $commissionPercent) / 100, 2);
                $ride->driver_amount = $ride->total_pay - round(($ride->total_pay * $commissionPercent) / 100, 2);
                $ride->save();

                if ($user->pending_refer_discount > 0) {
                    $user_refer_history = UserReferHistory::query()->where('user_id', $user->id)->where('user_status', 0)->first();
                    if ($user_refer_history != Null) {
                        if ($user_refer_history->user_discount_type == 1) {
                            $refer_discount_price = round($ride->total_pay - $user_refer_history->user_discount, 2);
                            $get_refer_discount_price = $user_refer_history->user_discount;
                        } else {
                            $refer_discount_price = round((($ride->total_pay * $user_refer_history->user_discount) / 100), 2);
                            $get_refer_discount_price = $refer_discount_price;
                            $refer_discount_price = round($ride->total_pay - $refer_discount_price, 2);
                        }
                        if ($refer_discount_price < 0) {
                            $refer_discount_price = 0;
                        }
                        $ride->total_pay = $refer_discount_price;
                        $ride->user_refer_history_id = $user_refer_history->id;
                        $ride->refer_discount = round($get_refer_discount_price, 2);
                        $ride->admin_commission = $ride->admin_commission - round($get_refer_discount_price, 2);
                        $ride->save();
                        $user_refer_history->user_status = 1;
                        $user_refer_history->save();
                        $user->pending_refer_discount = $user->pending_refer_discount - 1;
                        $user->save();
                    } else {
                        $user_refer_history = UserReferHistory::query()->where('refer_id', $user->id)->where('refer_status', 0)->first();
                        if ($user_refer_history != Null) {
                            if ($user_refer_history->refer_discount_type == 1) {
                                $refer_discount_price = round($ride->total_pay - $user_refer_history->refer_discount, 2);
                                $get_refer_discount_price = $user_refer_history->refer_discount;
                            } else {
                                $refer_discount_price = round((($ride->total_pay * $user_refer_history->refer_discount) / 100), 2);
                                $get_refer_discount_price = $refer_discount_price;
                                $refer_discount_price = round($ride->total_pay - $refer_discount_price, 2);
                            }
                            if ($refer_discount_price < 0) {
                                $refer_discount_price = 0;
                            }
                            $ride->total_pay = $refer_discount_price;
                            $ride->refer_discount = round($get_refer_discount_price, 2);
                            $ride->admin_commission = $ride->admin_commission - round($get_refer_discount_price, 2);
                            $ride->user_refer_history_id = $user_refer_history->id;
                            $ride->save();
                            $user_refer_history->refer_status = 1;
                            $user_refer_history->save();
                            $user->pending_refer_discount = $user->pending_refer_discount - 1;
                            $user->save();
                        }
                    }
                }

                if ($ride->ride_type == 0) {
                    ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                    $provider_running_service = new ProviderUserRunningService();
                    $provider_running_service->provider_id = $driver_id;
                    $provider_running_service->user_id = $ride->user_id;
                    $provider_running_service->booking_id = $ride->id;
                    $provider_running_service->save();
                }

                $notification_log = $this->UserTransportNotification($ride->id, $user->device_token, $ride->status, $user->login_device, $user->language);

                $general_settings = request()->get("general_settings");
                if ($general_settings != Null) {
                    if ($general_settings->send_mail == 1) {
                        $user_name = ucwords($ride->user_name);
                        $driver_name = ucwords($driver_detail->driver_name);
                        $pickup_address = $ride->pickup_address;
                        $destination_address = $ride->destination_address;
                        $pickup_datetime = date("Y-m-d h:i:s", strtotime($ride->pickup_datetime));

                        //sending mail driver
                        try {
                            $mail_type = "driver_new_ride_request_–_transport";
                            $to_mail = $driver_detail->email;
                            $subject = "You have a new " . $general_settings->mail_site_name . "Ride Request";
                            $disp_data = array("##driver_name##" => $driver_name,
                                "##pickup_location##" => $pickup_address, "##destination_location##" => $destination_address,
                                "##pickup_time_location##" => $pickup_datetime, "##user_name##" => $user_name);
                            $mail_return_data = $this->sendMail($subject, $to_mail, $mail_type, $disp_data);
                        } catch (\Exception $e) {
                        }

                        try{
                            $mail_type = "new_order_placed-transport";
                            $to_mail = $user->email;
                            $subject = "Your ".$general_settings->mail_site_name." Ride Request Placed";
                            $disp_data = array("##user_name##"=>$user_name);
                            $mail_return_data = $this->sendMail($subject,$to_mail,$mail_type,$disp_data);
                        } catch (\Exception $e){}
                        try{
                            if ($general_settings->send_receive_email != Null) {
                                $pickup_datetime =  $ride->pickup_datetime;
                                $driver_name = ucwords($driver_detail->driver_name);
                                $mail_type = "admin_new__ride_request_-_transport";
                                $to_mail = $general_settings->send_receive_email;
                                $subject = $driver_name.", got a new ride request" ;
                                $disp_data = array("##driver_name##"=>$driver_name,
                                    "##pickup_location##"=>$pickup_address,"##destination_location##"=>$destination_address,
                                    "##pickup_time_location##"=>$pickup_datetime,"##user_name##"=>$user_name);
                                $mail_return_data = $this->sendMail($subject,$to_mail,$mail_type,$disp_data);
                            }
                        } catch (\Exception $e){}
                    }
                }

                return response()->json([
                    "status" => 1,
                    //"message" => "success!",
                    "message" => __('driver_messages.1'),
                    "message_code" => 1,
                    "noti_log" => $notification_log
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    //"message" => "something went to wrong!",
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }

        } else {
            return response()->json([
                "status" => 0,
//                "message" => "ride not found!",
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    public function driverApproveDocumentNotification($document_id, $device_token, $title = '', $message = '', $is_approved = 0, $login_device = null)
    {
        return $this->sendEventPushToToken(
            'driver_document_approved',
            $device_token,
            'es',
            [],
            [
                'document_id' => (string) $document_id,
                'is_approved' => '1',
            ],
            $login_device
        );
    }

    public function driverRejectDocumentNotification($document_id, $device_token, $title = '', $message = '', $login_device = null)
    {
        return $this->sendEventPushToToken(
            'driver_document_rejected',
            $device_token,
            'es',
            [],
            [
                'document_id' => (string) $document_id,
                'is_approved' => '0',
            ],
            $login_device
        );
    }

    public function driverBidRejectNotification($device_token,$language, $login_device = null)
    {
        return $this->sendEventPushToToken(
            'driver_bid_rejected',
            $device_token,
            $language ?: 'es',
            [],
            [],
            $login_device
        );
    }

    /**
     * @param  array<string, string|int|float>  $vars
     * @return array{title: string, message: string, title_code: int, message_code: int, notification_type: int, sound: string}
     */
    private function applyEventTemplate(string $eventKey, ?string $lang = 'es', array $vars = []): array
    {
        $lang = $lang ?: 'es';
        $tpl = PushEventTemplateHelper::resolve($eventKey, $lang, $vars);
        if ($tpl['title'] === '' && $tpl['message'] === '') {
            return [
                'title' => __('user_messages.91', [], $lang),
                'message' => __('user_messages.9', [], $lang),
                'title_code' => 91,
                'message_code' => 9,
                'notification_type' => 1,
                'sound' => 'default',
            ];
        }

        return [
            'title' => $tpl['title'],
            'message' => $tpl['message'],
            'title_code' => $tpl['title_code'],
            'message_code' => $tpl['message_code'],
            'notification_type' => $tpl['notification_type'],
            'sound' => $tpl['sound'],
        ];
    }

    private function passengerRideEventKey(int $rideStatus, TransportRideBook $ride, int $completedBy = 0): ?string
    {
        if ($rideStatus === 4) {
            return 'passenger_ride_cancelled';
        }
        if ($rideStatus === 5 && (int) $ride->is_way_point === 1 && (int) $ride->way_point_status > 0) {
            return 'passenger_ride_waypoint';
        }

        return match ($rideStatus) {
            1 => 'passenger_ride_accepted',
            2 => 'passenger_ride_scheduled_accepted',
            3 => 'passenger_driver_at_pickup',
            5 => 'passenger_ride_started',
            6 => 'passenger_ride_at_destination',
            7, 9 => 'passenger_ride_completed',
            default => null,
        };
    }

    private function driverRideEventKey(int $rideStatus, TransportRideBook $ride): ?string
    {
        if ($rideStatus === 4) {
            return 'driver_passenger_cancelled';
        }
        if ($rideStatus === 5 && (int) $ride->is_way_point === 1 && (int) $ride->way_point_status > 0) {
            return null;
        }

        return match ($rideStatus) {
            7, 9 => 'driver_ride_completed',
            default => null,
        };
    }

    /**
     * @param  array<string, string|int|float>  $vars
     * @param  array<string, string>  $extraData
     */
    private function sendEventPushToToken(string $eventKey, ?string $token, ?string $lang = 'es', array $vars = [], array $extraData = [], ?int $loginDevice = null): mixed
    {
        if ($token === null || trim($token) === '') {
            return null;
        }
        $event = $this->applyEventTemplate($eventKey, $lang, $vars);
        if ($event['title'] === '' && $event['message'] === '') {
            return null;
        }
        if (! isset($extraData['user_type'])) {
            if (str_starts_with($eventKey, 'driver_')) {
                $extraData['user_type'] = '2';
            } elseif (str_starts_with($eventKey, 'passenger_')) {
                $extraData['user_type'] = '1';
            }
        }
        $payload = array_merge([
            'title' => $event['title'],
            'title_code' => (string) $event['title_code'],
            'sound' => 'true',
            'notification_type' => (string) $event['notification_type'],
            'message' => $event['message'],
            'body' => $event['message'],
            'message_code' => (string) $event['message_code'],
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'dispatch_action' => $this->dispatchActionForEventKey($eventKey),
            'dispatch_ts' => (string) time(),
        ], $extraData);

        return FcmPushHelper::sendToTokenForLoginDevice($token, $event['title'], $event['message'], $payload, $event['sound'], $loginDevice);
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, string|int|float>  $vars
     * @param  array<string, string>  $extraData
     */
    private function sendEventPushToTokens(string $eventKey, array $tokens, ?string $lang = 'es', array $vars = [], array $extraData = []): void
    {
        $tokens = array_values(array_filter(
            $tokens,
            static fn ($token) => $token !== null && trim((string) $token) !== ''
        ));
        if ($tokens === []) {
            return;
        }

        $event = $this->applyEventTemplate($eventKey, $lang, $vars);
        if ($event['title'] === '' && $event['message'] === '') {
            return;
        }
        if (! isset($extraData['user_type'])) {
            if (str_starts_with($eventKey, 'driver_')) {
                $extraData['user_type'] = '2';
            } elseif (str_starts_with($eventKey, 'passenger_')) {
                $extraData['user_type'] = '1';
            }
        }
        $payload = array_merge([
            'title' => $event['title'],
            'title_code' => (string) $event['title_code'],
            'sound' => 'true',
            'notification_type' => (string) $event['notification_type'],
            'message' => $event['message'],
            'body' => $event['message'],
            'message_code' => (string) $event['message_code'],
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'dispatch_action' => $this->dispatchActionForEventKey($eventKey),
            'dispatch_ts' => (string) time(),
        ], $extraData);

        FcmPushHelper::sendToTokens($tokens, $event['title'], $event['message'], $payload, $event['sound'], false);
    }

    private function dispatchActionForEventKey(string $eventKey): string
    {
        return match ($eventKey) {
            'driver_new_request' => 'refresh_available_rides',
            'passenger_driver_bid' => 'refresh_driver_bids',
            'driver_fare_changed_by_user' => 'refresh_available_rides',
            'driver_passenger_cancelled', 'passenger_ride_cancelled' => 'refresh_ride_status',
            'passenger_ride_accepted', 'driver_ride_completed', 'passenger_ride_started',
            'passenger_driver_at_pickup', 'passenger_ride_at_destination', 'passenger_ride_completed' => 'refresh_ride_status',
            default => 'refresh_ride_status',
        };
    }

}
