<?php

namespace App\Http\Controllers\Api\Transport;

use App\Classes\AdminClass;
use App\Helpers\DestinationPaymentHelper;
use App\Helpers\RideKindHelper;
use App\Helpers\RideSessionHelper;
use App\Helpers\WalletSettlementHelper;
use App\Classes\NotificationClass;
use App\Classes\UserClassApi;
use App\Models\AdminAreaList;
use App\Models\DriverBid;
use App\Models\ProviderBankDetails;
use App\Models\ProviderDocuments;
use App\Models\ProviderUserRunningService;
use App\Models\RequiredDocuments;
use App\Models\RestrictedArea;
use App\Models\SearchRadius;
use App\Models\ServiceSettings;
use App\Models\Sos;
use App\Models\TransportCourierDetails;
use App\Models\TransportDriverDetails;
use App\Models\TransportVehicleType;
use App\Models\TransportRatings;
use App\Models\TransportRideBook;
use App\Models\User;
use App\Support\ApiValidationRules;
use App\Models\UserCardDetails;
use App\Models\UserRatings;
use App\Models\UserReferHistory;
use App\Models\UserRideWayPoint;
use App\Models\UserRunningRide;
use App\Models\UserWalletTransaction;
use App\Models\VehicleService;
use App\Helpers\AcarreoHelper;
use App\Helpers\DriverVehicleHelper;
use App\Helpers\ServiceCatalogHelper;
use App\Models\WorldCurrency;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Rules\ColombianVehiclePlate;
use App\Support\ColombiaFormValidation;
use App\Helpers\AppMobileSettingsHelper;
use App\Helpers\RideInvoiceHelper;
use App\Helpers\VehicleCommissionHelper;
use App\Rules\ColombianNationalId;

class UserController extends Controller
{

    //ApiLogDetail logger type => 0:user,1:store,2:driver,3:provider
    private $userClassapi;
    private $notificationClass;
    private $adminClass;
    private $user_type = 0;

    public function __construct(UserClassApi $userClassapi, NotificationClass $notificationClass, AdminClass $adminClass)
    {
        $this->adminClass = $adminClass;
        $this->userClassapi = $userClassapi;
        $this->notificationClass = $notificationClass;
    }

    /**
     * checkUserAllow / checkDriverRegisterAllow return JsonResponse on failure, User model on success.
     */
    private function returnJsonIfAuthFailed(mixed $check): ?\Illuminate\Http\JsonResponse
    {
        return $check instanceof \Illuminate\Http\JsonResponse ? $check : null;
    }

    public function postTransportRideBooking(Request $request) {
        $this->notificationClass->ApiLogDetail($logger_type =0, $request->get('user_id'), "postTransportRideBooking", $request->all());
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
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
        $ride_book = $this->userClassapi->TransportRideBook($request, $user_check);
        return $ride_book;
    }

    public function postTransportCancelRide(Request $request)
    {
        $this->notificationClass->ApiLogDetail($logger_type = 0, $request->get('user_id'), 'postTransportCancelRide', $request->all());

        return $this->cancelTransportRide($request);
    }

    private function cancelTransportRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'access_token' => 'required|string',
            'ride_id' => 'required|numeric',
            'cancel_reason' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        return $this->userClassapi->transportRideBookingCancel(
            $request->get('ride_id'),
            'user',
            $request->get('cancel_reason')
        );
    }

    public function postTransportRideHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
                "filter_type" => "nullable|in:0,1,2,3,4,5",
                "order_status" => "nullable",
                "timezone" => "required"
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
        $user_currency = \App\Support\UserCurrencyResolver::forCurrency($user_check->currency);
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency->ratio;

        $lang_prefix = $this->adminClass->get_langugae_fields($user_check->language);
        $timezone = $this->notificationClass->getDefaultTimeZone($request->get('timezone'));
        date_default_timezone_set($timezone);

        $filter_type = $request->get('filter_type');
        $date = date('Y-m-d');
        //making an array from string
        $order_status =[];
        if($request->get('order_status') != ""){
            $order_status =  explode(",",$request->get('order_status'));
        }

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
        } elseif ($filter_type == 5) {
            //upcoming order
            $start_date = date('Y-m-d', strtotime('+1 days', strtotime($date)));
            $end_date = date('Y-m-d', strtotime('+365 days', strtotime($date)));
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        } else {//$filter_type == 0//all order
            //last 365 day
            $start_date = date('Y-m-d', strtotime('-365 days', strtotime($date)));
            $end_date = $date;
            $start_date = $start_date . " 00:00:01";
            $end_date = $end_date . " 23:59:59";
        }

        $per_page = 10;
        if ($request->get('per_page') != Null) {
            $per_page = $request->get('per_page');
        }
        $page = $request->get('page') != Null ? $request->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        $rides_1 = TransportRideBook::query()->select('user_ride_booking.id',
            'user_ride_booking.driver_id',
            'user_ride_booking.ride_no',
            'user_ride_booking.pickup_datetime',
            'user_ride_booking.total_pay',
            'user_ride_booking.status',
            'user_ride_booking.pickup_address',
            'user_ride_booking.destination_address',
            'users.first_name as driver_name',
            'users.avatar as driver_profile',
            'transport_vehicle_type.service_id',
            DB::raw("DATE_FORMAT(user_ride_booking.created_at, '%Y-%m-%d %H:%i:%s') as service_date_time"),
            DB::raw("DATE_FORMAT(user_ride_booking.pickup_datetime, '%Y-%m-%d %H:%i:%s') as schedule_order_date_time"),
            'vehicle_services.'.$lang_prefix.'name as service_name',
            )
            ->join('users','users.id','=','user_ride_booking.driver_id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','user_ride_booking.vehicle_type_id')
            ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
            ->where('user_ride_booking.user_id', $request->get('user_id'))
            ->where(function ($query) use ($request) {
                $query->where('user_ride_booking.driver_id', '!=', Null);
            })
            ->whereNotIn('user_ride_booking.status', [10,11])
            ->orderBy('user_ride_booking.pickup_datetime', 'desc');
        if ($filter_type != 5 && $filter_type != 0) {
            $rides_1 = $rides_1->where('user_ride_booking.pickup_datetime', '>=', $start_date)
                ->where('user_ride_booking.pickup_datetime', '<=', $end_date);
        }
        if ($filter_type == 5){
            $rides_1 = $rides_1->where('user_ride_booking.pickup_datetime', '>=', $start_date)
                ->whereIn('user_ride_booking.status', [0, 1, 2, 3, 5, 6, 7]);
        }

        //applying order status filter
        //order_status= 1-on-going, 2-completed, 3-cancelled, 4-pending
        if($request->get('order_status') != null){
            $ride_status_array=[];

            if(in_array(1,$order_status)){
                //on-going rides filter
                $ride_status_array=array_merge($ride_status_array,[ 3, 5, 6, 7, 8]);
            }
            if (in_array(2,$order_status)){
                //completed rides filter
                array_push($ride_status_array,9);
            }
            if (in_array(3,$order_status)){
                //cancelled rides filter
                array_push($ride_status_array,4);
            }
            if (in_array(4,$order_status)){
                //pending rides filter
                array_push($ride_status_array,0);
            }
            $rides_1 = $rides_1->whereIn('user_ride_booking.status', $ride_status_array);
        }


        $total_count = $rides_1->count();
        $rides_1 = $rides_1->get()->toArray();

        $ride_history = [];
        $rides = $rides_1;
        $rides = array_slice($rides,$offset,$per_page);
        usort($rides, function ($a, $b) {
            if ($a['pickup_datetime'] == $b['pickup_datetime']) return 0;
            return $a['pickup_datetime'] < $b['pickup_datetime'] ? 1 : -1;
        });
        foreach ($rides as $key => $ride) {
            $ride_history[] = [
                "ride_id" => $ride['id'],
                "booking_no" => $ride['ride_no'],
                "ride_status" => $ride['status'],
                "total_pay" => \App\Helpers\TripAmountHelper::resolveForCurrency($ride, (float) $currency),
                'service_date_time' => $ride['service_date_time'],
                'schedule_order_date_time' => $ride['schedule_order_date_time'],
                "pickup_address" => $ride['pickup_address'],
                "destination_address" => $ride['destination_address'],
                "driver_name" => $ride['driver_name'],
                "service_id" => $ride['service_id'],
                "driver_profile" => $ride['driver_profile'] != Null ? url('/assets/images/profile-images/customer/'.$ride['driver_profile']) : '',
                "service_name" => $ride['service_name'],
            ];
        }

        $current_page = $page;
        $divider = round($total_count / $per_page);
        if($divider > ($total_count / $per_page)){
            $last_page = $divider;
        } else{
            $last_page = $divider + 1;
        }

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            'current_page' => $current_page - 0,
            'last_page' => $last_page - 0,
            'total' => $total_count - 0,
            "rides" => $ride_history,
        ]);

    }

    public function postTransportRideCompletedInvoiceDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
                "ride_id" => "required|numeric"
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
        $ride_invoice_details = $this->userClassapi->findRideDetailsforTADorTCIorTRD($request->get('ride_id'), false, true, false);
        return $ride_invoice_details;
    }

    public function postTransportRideReceiptDetails(Request $request)
    {
        $this->notificationClass->ApiLogDetail($logger_type =0, $request->get('user_id'), "postTransportRideReceiptDetails", $request->all());
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric"
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
        $ride_invoice_details = $this->userClassapi->findRideDetailsforTADorTCIorTRD($request->get('ride_id'), false, false, true);
        return $ride_invoice_details;
    }

    public function postTransportRideUserRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "driver_id" => "required",
            "rating" => "required",
            "comment" => "nullable",
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
        $driver_rating = TransportRatings::query()->where('ride_id', $request->get('ride_id'))->where('status',1)->where('user_id', $request->get('user_id'))->first();
        if ($driver_rating == Null) {
            $check_ride = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();
            if ($check_ride == Null) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.26'),
                    "message_code" => 26,
                ]);
            }
            if ($check_ride->payment_status != 1) {
                if (! WalletSettlementHelper::markCashRidePaidIfNeeded(
                    $check_ride,
                    $this->notificationClass,
                    request()->get('general_settings')
                )) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('user_messages.83'),
                        "message_code" => 83,
                    ]);
                }
            }

            $driver_details_id = TransportDriverDetails::query()->select('user_id')
                ->where('user_id', $request->get('driver_id'))
                ->first();
            if ($driver_details_id == Null) {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.15'),
                    "message_code" => 15,
                ]);
            }
            $check_driver = TransportDriverDetails::query()->where('user_id', $driver_details_id->user_id)->first();
            if ($check_driver != Null) {
                $check_ride->user_rating_status = 1;
                $check_ride->save();

                $driver_rating = TransportRatings::query()->where('driver_id', $check_driver->user_id)->where('ride_id', $request->get('ride_id'))->where('user_id', $request->get('user_id'))->where('status',1)->first();
                if ($driver_rating == Null) {
                    $driver_rating = new TransportRatings();
                }
                $driver_rating->driver_id = $check_driver->user_id;
                $driver_rating->user_id = $request->get('user_id');
                $driver_rating->ride_id = $request->get('ride_id');
                $driver_rating->rating = round($request->get('rating'),2);
                if ($request->get('comment') != Null) {
                    $driver_rating->comment = $request->get('comment');
                }
                $driver_rating->save();

                $ratings = TransportRatings::query()->select(DB::raw('avg(rating) as ratings'))->groupBy('driver_id')->where('driver_id', $check_driver->user_id)->where('status',1)->first();
                $check_driver->rating = round($ratings->ratings,2);
                $check_driver->save();
                UserRunningRide::query()->where('user_id', $check_ride->user_id)->where('booking_id', $check_ride->id)->delete();
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('user_messages.15'),
                    "message_code" => 15,
                ]);
            }
        }
        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
        ]);
    }

    public function postTransportOrderPayment(Request $request) {
        //payment_method 1 = omise, 0= paypal
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "payment_type" => "required|numeric|in:1,2,3",
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

        $ride_details = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();
        if ($ride_details == Null) {
            return response()->json([
                "status" => 0,
//                "message" => "ride details not found",
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }

        $redirect_url = "";
        $success_url = "";
        $failed_url = "";
        $error_url = "";
        if ($request->get('payment_type') == 1) {
            WalletSettlementHelper::settleDriverCommissionOnCashRide(
                $ride_details,
                $this->notificationClass,
                request()->get('general_settings')
            );
            //end code for add amount in driver wallet
            $ride_details->payment_type = $request->get('payment_type');
            $ride_details->payment_status = 1;
            $ride_details->save();
            UserRunningRide::query()->where('user_id', $ride_details->user_id)->where('booking_id', $ride_details->id)->delete();
        }
        elseif ($request->get('payment_type') == 2) {
            if ($ride_details->payment_status == 0) {
                if($ride_details->driver_pay_settle_status != 1) {
                    if (request()->get('general_settings')->auto_settle_wallet == 1) {
                        $driver_data = TransportDriverDetails::query()->select('transport_driver_details.user_id as provider_id')
                            ->where('transport_driver_details.user_id', $ride_details->driver_id)->first();

                        if ($driver_data != Null) {
                            $provider_id = $driver_data->provider_id;
                            $wallet_provider_type = 0;
                            $transaction_type = 1;
                            $add_update_wallet_bal = $ride_details->driver_amount;
                            $subject = "Credited by Admin -  Booking # " . $ride_details->ride_no;
                            $subject_code = 15;
                            $order_no = $ride_details->ride_no;
                            $driver_wallet_update = $this->notificationClass->providerUpdateWalletBalance($provider_id, $wallet_provider_type, $transaction_type, $add_update_wallet_bal, $subject, $subject_code, $order_no);

                            if ($driver_wallet_update) {
                                $ride_details->driver_pay_settle_status = 1;
                            }
                        }
                    }
                }
                $ride_details->payment_type = $request->get('payment_type');
                $ride_details->payment_status = 1;
                $ride_details->save();
            }
            UserRunningRide::query()->where('user_id', $ride_details->user_id)->where('booking_id', $ride_details->id)->delete();
        }
        elseif ($request->get('payment_type') == 3) {
            if ($ride_details->payment_status == 0) {
                $wallet_balance = UserWalletTransaction::query()->where('user_id', $request->get('user_id'))->orderBy('id', 'desc')->first();
                if ($wallet_balance != Null) {
                    $balance = $wallet_balance->remaining_balance;
                } else {
                    $balance = 0;
                }
                if ($ride_details->total_pay > $balance) {
                    return response()->json([
                        "status" => 0,
//                        "message" => "You can't pay ride amount through Wallet because your wallet balance is insufficient.",
                        "message" => __('user_messages.109'),
                        "message_code" => 109
                    ]);
                }
                //code for deduct amount in user wallet
                $add_balance = new UserWalletTransaction();
                $add_balance->user_id = $request->get('user_id');
                $add_balance->wallet_provider_type = $this->user_type;
                $add_balance->transaction_type = 2;
                $add_balance->amount = $ride_details->total_pay;
                $add_balance->subject = "Paid to " . $ride_details->driver_name . " - Booking # " . $ride_details->ride_no;
                $add_balance->remaining_balance = round($balance - $ride_details->total_pay, 2);
                $add_balance->subject_code = 4;
                $add_balance->order_no = $ride_details->ride_no;
                $add_balance->save();
                //End code for deduct amount in user wallet
                //code for add amount in driver wallet
                if($ride_details->driver_pay_settle_status != 1) {
                    if (request()->get('general_settings')->auto_settle_wallet == 1) {
                        $driver_data = TransportDriverDetails::query()->select('transport_driver_details.user_id as provider_id')
                            ->where('transport_driver_details.user_id', $ride_details->driver_id)->first();

                        if ($driver_data != Null) {
                            $provider_id = $driver_data->provider_id;
                            $wallet_provider_type = 0;
                            $transaction_type = 1;
                            $add_update_wallet_bal = $ride_details->driver_amount;
                           // $subject = "Credited by " . $ride_details->user_name . " -  Booking # " . $ride_details->ride_no;
                            $subject = "Credited by Admin -  Booking # " . $ride_details->ride_no;
                            $subject_code = 15;
                            $order_no = $ride_details->ride_no;
                            $driver_wallet_update = $this->notificationClass->providerUpdateWalletBalance($provider_id, $wallet_provider_type, $transaction_type, $add_update_wallet_bal, $subject, $subject_code, $order_no);

                            if ($driver_wallet_update) {
                                $ride_details->driver_pay_settle_status = 1;
                            }
                        }
                    }
                }
                //end code for add amount in driver wallet

                $ride_details->payment_type = $request->get('payment_type');
                $ride_details->payment_status = 1;
                $ride_details->save();
            }
            UserRunningRide::query()->where('user_id', $ride_details->user_id)->where('booking_id', $ride_details->id)->delete();
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "something went to wrong",
                "message" => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }
        if ($request->get('payment_type') != 2) {
            if ($ride_details->completed_by == 1) {
                if ($ride_details->driver_id != Null) {
                    $driver_details = TransportDriverDetails::query()->where('user_id', $ride_details->driver_id)->first();
                    if ($driver_details != Null) {
                            //$update_driver_status->current_status = 1;
                            //$update_driver_status->save();
                            ProviderUserRunningService::query()->where('user_id', $ride_details->user_id)->where('booking_id', $ride_details->id)->delete();
                    }
                }
                $ride_details->status = 9;
                $ride_details->save();
            }
        }

        return response()->json([
            "status" => 1,
//            "message" => "success!",
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "ride_id" => $ride_details->id,
            "ride_no" => $ride_details->ride_no,
            "redirect_url" => $redirect_url,
            "success_url" => $success_url,
            "failed_url" => $failed_url,
            "error_url" => $error_url
        ]);
    }


    //Driver Mode
    public function postDriverUpdateCurrentLatLong(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "current_lat" => "required",
            "current_long" => "required",
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

        $update_driver_lat_long = TransportDriverDetails::query()->where('user_id', $request->get('user_id'))->first();
        if ($update_driver_lat_long != Null) {
            $update_driver_lat_long->current_lat = $request->get('current_lat');
            $update_driver_lat_long->current_long = $request->get('current_long');
            $update_driver_lat_long->last_online_date_time = date('Y-m-d H:i:s');
            $update_driver_lat_long->save();

            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
            ]);
        } else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }
    }

    public function postVehicleServiceList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
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
        $language = $driver_check->language;
        if ($language != "en" && $language != "" && $language != "Null") {
            $lang_prefix = $language . "_";
        } else {
            $lang_prefix = "";
        }
        $service_icon_url = url('/assets/images/vehicle-service/');
        $vehicle_icon_url = url('/assets/images/service-category/transport-service-type/');
        $vehicle_service_array = \App\Helpers\DriverVehicleHelper::registrationServiceList(
            $lang_prefix,
            $service_icon_url,
            $vehicle_icon_url
        );
        if ($vehicle_service_array !== []) {
            return response()->json([
                "status" => 1,
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "service_list" => $vehicle_service_array
            ]);
        } else {
             return response()->json([
                    'success' => 0,
                    'message' => "Vehicle Service Currently Not Available",
            ]);
        }
    }

    public function postRequiredDocumentList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
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

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        return $this->userClassapi->getDriverRequiredDocumentList($user_details->id, $user_details->driver_doc_status, $user_details->is_driver_type);
    }

    public function postUploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "required|string",
            "document_id" => "required|integer",
//            "document_file" => "required|file",
            "is_update"=>"required|integer|in:0,1",
            "document_file" => "required_if:is_update,==,0|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120",
            "expiry_date" => "nullable|date",
            "document_number" => ["nullable", new ColombianNationalId()],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 0,
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
        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        $site_setting = request()->get('general_settings');
        $doc_status = 0;
        $driver_type = 0;
        $req_document = RequiredDocuments::query()->where('id', $request->get('document_id'))->where('status', 1)->first();
        if ($req_document != Null) {
            $upload_document = ProviderDocuments::query()->where('req_document_id', $req_document->id)->where('user_id',$request->get('user_id'))->first();
            $file_new = "";
            if ($request->file('document_file') != Null) {
                if ($upload_document != Null) {
                    if (\File::exists(public_path('/assets/images/provider-documents/' . $upload_document->document_file))) {
                        \File::delete(public_path('/assets/images/provider-documents/' . $upload_document->document_file));
                    }
                }
                $file = $request->file('document_file');
                $file_new = rand(1, 9) . date('sihYdm') . rand(1, 9) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/images/provider-documents/', $file_new);
            } else {
                if ($upload_document == Null) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.9'),
                        "message_code" => 9,
                    ]);
                }
            }
            if ($upload_document == Null) {
                $upload_document = new ProviderDocuments();
            }
            $upload_document->user_id = $user_details->id;
            $upload_document->req_document_id = $req_document->id;
            if($file_new != "") {
                $upload_document->document_file = $file_new;
            }
            $upload_document->expiry_date = ($request->get('expiry_date') != null) ?
                Carbon::parse($request->get('expiry_date'))->format('Y-m-d') : null;
            $upload_document->status = 0;
            $upload_document->save();
            if ($user_details->driver_doc_status == 1) {
                $doc_status = 1;
            } else {
                $req_document_count = RequiredDocuments::query()->where('status', 1)->count();
                $uploaded_document_count = ProviderDocuments::query()->where('user_id', $user_details->id)->count();
                if ($req_document_count == $uploaded_document_count) {
                    $user_details->driver_doc_status = 1;
                    $user_details->save();
                    $doc_status = 1;

                    if($user_details->driver_doc_status == 1 && $user_details->driver_vehicle_status == 1){
                        $user_details->is_driver_type = 1;
                        if($site_setting->auto_approve == 1){
                            $user_details->is_driver_status = 1;
                        }else{
                            $user_details->is_driver_status = 0;
                        }
                        $user_details->save();
                        $driver_type = 1;
                    }

                    $general_settings = request()->get("general_settings");
                    if ($general_settings != Null && $general_settings->send_receive_email != Null) {
                        $email = $general_settings->send_receive_email;
                        $driver_name = ($user_details->first_name != "") ? $user_details->first_name : "";
                        try {
                            $mail_type = "admin_new_driver_signup";
                            $to_mail = $email;
                            $subject = "New Driver Registered";
                            $disp_data = array("##driver_name##" => $driver_name);
                            $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
            return $this->userClassapi->getDriverRequiredDocumentList($user_details->id, $doc_status, $driver_type);
        } else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.9'),
                "message_code" => 9,
            ]);
        }
    }

    public function postServiceRegister(Request $request)
    {
        $requiresPlate = $this->driverRegistrationRequiresPlate($request);
        $plateRules = $requiresPlate
            ? ['required', new ColombianVehiclePlate((int) $request->get('vehicle_type_id'))]
            : ['nullable', 'string', 'max:20'];

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => \App\Support\ApiValidationRules::ACCESS_TOKEN,
            "vehicle_type_id" => "required|numeric",
            "manufacture_name" => "required",
            "model_name" => "required",
            "model_year" => "required|integer|min:1950|max:" . (date('Y') + 1),
            "technical_inspection_expiry" => "nullable|date|after_or_equal:today",
            "vehicle_plat_no" => $plateRules,
            "vehicle_color" => "required",
            // TEMP: allow legacy app (single vehicle_image) until all clients send 3 angles.
            "vehicle_image" => "nullable",
            "vehicle_image_front" => "nullable",
            "vehicle_image_side" => "nullable",
            "vehicle_image_rear" => "nullable",
            "current_lat" => "nullable",
            "current_long" => "nullable",
            "child_safety_seat" => "nullable",
            "handy_cap_seat" => "nullable",
            "is_taxi" => "nullable|in:0,1",
            "accept_delivery" => "nullable|in:0,1",
            "accept_encomiendas" => "nullable|in:0,1",
            "also_transport_passengers" => "nullable|in:0,1",
            "delivery_variant" => "nullable|string|in:motoraton,motocarro,bicycle,motocarguero,camion,jaula",
        ]);
        if ($validator->fails()) {
            if ($validator->errors()->has('vehicle_plat_no')) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.371'),
                    "message_code" => 371,
                ]);
            }
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 0,
            ]);
        }

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->returnJsonIfAuthFailed($user_check)) {
            return $failed;
        }

        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->returnJsonIfAuthFailed($driver_check)) {
            return $failed;
        }

        $deliveryVariant = $request->filled('delivery_variant')
            ? (string) $request->get('delivery_variant')
            : null;
        $isAcarreoVariant = AcarreoHelper::normalizeVariant($deliveryVariant) !== null;

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
        $site_setting = request()->get('general_settings');
        $driver_type = 0;

        $add_driver_vehicle_details = TransportDriverDetails::query()->where('user_id',$user_details->id)->first();
        if($add_driver_vehicle_details == Null){
            $add_driver_vehicle_details = new TransportDriverDetails();
            $add_driver_vehicle_details->user_id = $user_details->id;
            $add_driver_vehicle_details->save();

            $user_details->driver_vehicle_status = 1;
            $user_details->save();

            $req_document_count = RequiredDocuments::query()->where('status', 1)->count();
            if($req_document_count == 0){
                $user_details->driver_doc_status = 1;
                $user_details->save();
            }

            if($user_details->driver_doc_status == 1 && $user_details->driver_vehicle_status == 1){
                $user_details->is_driver_type = 1;
                if($site_setting->auto_approve == 1){
                    $user_details->is_driver_status = 1;
                }else{
                    $user_details->is_driver_status = 0;
                }
                $user_details->save();
                $driver_type = 1;
            }
        }

        $vehiclePhotoFields = ['vehicle_image', 'vehicle_image_front', 'vehicle_image_side', 'vehicle_image_rear'];
        $hasVehiclePhotoUpload = false;
        foreach ($vehiclePhotoFields as $photoField) {
            if ($request->hasFile($photoField) && $request->file($photoField)->isValid()) {
                $hasVehiclePhotoUpload = true;
                break;
            }
        }
        $hasExistingVehiclePhoto = collect($vehiclePhotoFields)
            ->contains(fn (string $field) => !empty($add_driver_vehicle_details->{$field}));
        if (!$hasVehiclePhotoUpload && !$hasExistingVehiclePhoto) {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.372'),
                "message_code" => 9,
            ]);
        }

        foreach ($vehiclePhotoFields as $photoField) {
            $saved = $this->storeDriverVehiclePhoto($request, $photoField, $add_driver_vehicle_details->{$photoField} ?? null);
            if ($saved !== null) {
                $add_driver_vehicle_details->{$photoField} = $saved;
            }
        }
        if ($add_driver_vehicle_details->vehicle_image_front) {
            $add_driver_vehicle_details->vehicle_image = $add_driver_vehicle_details->vehicle_image_front;
        } elseif ($add_driver_vehicle_details->vehicle_image && empty($add_driver_vehicle_details->vehicle_image_front)) {
            $add_driver_vehicle_details->vehicle_image_front = $add_driver_vehicle_details->vehicle_image;
        }

        if($request->get('current_lat') != Null && $request->get('current_long') != Null){
            $add_driver_vehicle_details->current_lat =$request->get('current_lat');
            $add_driver_vehicle_details->current_long = $request->get('current_long');
        }
        $add_driver_vehicle_details->vehicle_type_id = $request->get('vehicle_type_id');
        $add_driver_vehicle_details->vehicle_company = $request->get('manufacture_name');
        $add_driver_vehicle_details->plat_no = $requiresPlate
            ? ColombiaFormValidation::normalizePlate($request->get('vehicle_plat_no'))
            : trim((string) $request->get('vehicle_plat_no', ''));
        $add_driver_vehicle_details->model_name = $request->get('model_name');
        $add_driver_vehicle_details->model_year = $request->get('model_year');
        if ($request->filled('technical_inspection_expiry')) {
            $add_driver_vehicle_details->technical_inspection_expiry = Carbon::parse(
                $request->get('technical_inspection_expiry')
            )->format('Y-m-d');
        } else {
            $add_driver_vehicle_details->technical_inspection_expiry = null;
        }
        $add_driver_vehicle_details->vehicle_color = $request->get('vehicle_color');
        $add_driver_vehicle_details->child_seat = $request->get('child_safety_seat') != Null ? $request->get('child_safety_seat') : 0;
        $add_driver_vehicle_details->handicap = $request->get('handy_cap_seat') != Null ? $request->get('handy_cap_seat') : 0;
        $vehicleType = TransportVehicleType::query()->find($request->get('vehicle_type_id'));
        $vehicleServiceId = (int) ($vehicleType->service_id ?? 0);
        $alsoTransportPassengers = (int) $request->get('also_transport_passengers', 0);
        $acceptDelivery = (int) $request->get('accept_delivery', 1);
        $acceptEncomiendas = (int) $request->get('accept_encomiendas', 1);

        if ($vehicleServiceId === 4
            || \App\Helpers\DriverVehicleHelper::isDeliveryOnlyRegistration($deliveryVariant, $vehicleServiceId)) {
            $add_driver_vehicle_details->accept_delivery = 1;
            $add_driver_vehicle_details->accept_encomiendas = $deliveryVariant === 'bicycle' ? 0 : $acceptEncomiendas;
            $add_driver_vehicle_details->accept_transport = 0;
            $add_driver_vehicle_details->also_transport_passengers = 0;
        } elseif (\App\Helpers\DeliveryVehicleHelper::serviceSupportsPassengerToggle($vehicleServiceId)) {
            $add_driver_vehicle_details->accept_delivery = $acceptDelivery;
            $add_driver_vehicle_details->accept_encomiendas = $acceptEncomiendas;
            $add_driver_vehicle_details->also_transport_passengers = $alsoTransportPassengers;
            // Carro / moto / motoratón must receive transport requests; toggle only affects extras (taxi, seats).
            $add_driver_vehicle_details->accept_transport = 1;
            if (!$alsoTransportPassengers) {
                $add_driver_vehicle_details->child_seat = 0;
                $add_driver_vehicle_details->handicap = 0;
                $add_driver_vehicle_details->is_taxi = 0;
            }
        } elseif ($isAcarreoVariant || (string) DB::table('vehicle_services')->where('id', $vehicleServiceId)->value('service_mode') === AcarreoHelper::MODE) {
            $add_driver_vehicle_details->accept_transport = 0;
            $add_driver_vehicle_details->accept_delivery = 0;
            $add_driver_vehicle_details->accept_encomiendas = 0;
            $add_driver_vehicle_details->also_transport_passengers = 0;
        } elseif ($vehicleServiceId === 7) {
            // Viajes compartidos (legacy id 7): transport + shared-ride offers.
            $add_driver_vehicle_details->accept_transport = 1;
            $add_driver_vehicle_details->accept_delivery = 0;
            $add_driver_vehicle_details->accept_encomiendas = 0;
            $add_driver_vehicle_details->also_transport_passengers = 0;
        } else {
            $add_driver_vehicle_details->accept_transport = 1;
            $add_driver_vehicle_details->accept_delivery = 0;
            $add_driver_vehicle_details->accept_encomiendas = 0;
        }

        $add_driver_vehicle_details->is_taxi = ($vehicleServiceId === 1 && (int) ($add_driver_vehicle_details->also_transport_passengers ?? 0) === 1)
            ? (int) $request->get('is_taxi', 0)
            : 0;

        if ((int) ($add_driver_vehicle_details->accept_delivery ?? 0) === 1
            && \Illuminate\Support\Facades\Schema::hasTable('vehicle_type_service_eligibility')) {
            \Illuminate\Support\Facades\DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                [
                    'vehicle_type_id' => (int) $request->get('vehicle_type_id'),
                    'service_id' => 4,
                ],
                ['updated_at' => now(), 'created_at' => now()]
            );
            if ($vehicleServiceId > 0 && $vehicleServiceId !== 4) {
                \Illuminate\Support\Facades\DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                    [
                        'vehicle_type_id' => (int) $request->get('vehicle_type_id'),
                        'service_id' => $vehicleServiceId,
                    ],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            $persistVariant = $deliveryVariant;
            if ($persistVariant === null || trim($persistVariant) === '') {
                $persistVariant = DriverVehicleHelper::resolveStoredDeliveryVariant((int) $request->get('vehicle_type_id'));
            }
            $add_driver_vehicle_details->delivery_variant = $persistVariant;
        }

        $add_driver_vehicle_details->save();


        $vehicle_icon_url = url('/assets/images/service-category/transport-service-type');
        $driver_vehicle_details = TransportDriverDetails::query()->select(
            'transport_vehicle_type.name as vehicle_type_name',
            DB::raw("(CASE WHEN transport_vehicle_type.icon_name!='' THEN concat('$vehicle_icon_url','/',transport_vehicle_type.icon_name) ELSE '' END) as vehicle_type_icon"))
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
            ->where('transport_driver_details.user_id', $user_details->id)
            ->first();

        if ($driver_vehicle_details == Null) {
            TransportDriverDetails::query()->where('id', $add_driver_vehicle_details->id)->delete();
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }


        return response()->json([
            'status' => 1,
            'message' => __('driver_messages.1'),
            "message_code" => 1,
            'vehicle_type_icon' => $driver_vehicle_details->vehicle_type_icon,
            'vehicle_type_name' => $driver_vehicle_details->vehicle_type_name,
            "is_driver_type" => $driver_type,
            "driver_vehicle_status" => $user_details->driver_vehicle_status,
            'driver_doc_status' =>  $user_details->driver_doc_status
        ]);
    }

    public function postDriverGetVehicleDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
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

        $driverSelect = [
            'vehicle_services.id as service_id',
            'transport_driver_details.vehicle_type_id',
            'transport_driver_details.vehicle_company',
            'transport_driver_details.model_name',
            'transport_driver_details.model_year',
            'transport_driver_details.technical_inspection_expiry',
            'transport_driver_details.plat_no',
            'transport_driver_details.vehicle_color',
            'transport_driver_details.vehicle_image',
            'transport_driver_details.vehicle_image_front',
            'transport_driver_details.vehicle_image_side',
            'transport_driver_details.vehicle_image_rear',
            'transport_driver_details.child_seat',
            'transport_driver_details.handicap',
            'transport_driver_details.also_transport_passengers',
            'transport_driver_details.is_taxi',
            'transport_vehicle_type.name as vehicle_type_name',
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            $driverSelect[] = 'transport_driver_details.delivery_variant';
        }

        $driver_details = TransportDriverDetails::query()->select($driverSelect)
                        ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
                        ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
                        ->where('transport_driver_details.user_id', $request->get('user_id'))->first();
        if($driver_details != Null){
            $deliveryVariant = DriverVehicleHelper::resolveStoredDeliveryVariant(
                (int) $driver_details->vehicle_type_id,
                $driver_details->delivery_variant ?? null
            );
            $registrationKey = DriverVehicleHelper::registrationKeyFromRequest(
                $deliveryVariant,
                (int) $driver_details->service_id,
                'transport'
            );

            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                "service_id" => $driver_details->service_id,
                "vehicle_type_id" => $driver_details->vehicle_type_id,
                "delivery_variant" => $deliveryVariant ?? '',
                "registration_key" => $registrationKey,
                "manufacture_name" => $driver_details->vehicle_company,
                "model_name" => $driver_details->model_name,
                "model_year" => $driver_details->model_year,
                "technical_inspection_expiry" => $driver_details->technical_inspection_expiry ?? '',
                "vehicle_plat_no" => $driver_details->plat_no,
                "vehicle_color" => $driver_details->vehicle_color != Null ? $driver_details->vehicle_color : '',
                "vehicle_image" => $driver_details->vehicle_image != null
                    ? url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image)
                    : '',
                "vehicle_image_front" => $driver_details->vehicle_image_front != null
                    ? url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image_front)
                    : '',
                "vehicle_image_side" => $driver_details->vehicle_image_side != null
                    ? url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image_side)
                    : '',
                "vehicle_image_rear" => $driver_details->vehicle_image_rear != null
                    ? url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image_rear)
                    : '',
                "child_safety_seat" => $driver_details->child_seat,
                "handy_cap_seat" => $driver_details->handicap,
                "also_transport_passengers" => (int) ($driver_details->also_transport_passengers ?? 0),
                "is_taxi" => (int) ($driver_details->is_taxi ?? 0),
            ]);

        }else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }
    }

    public function postDriverUpdateCurrentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => ApiValidationRules::ACCESS_TOKEN,
                "update_status" => "required|numeric|in:0,1"
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
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }
        $general_settings=request()->get('general_settings');
        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_check);

        if($request->get('update_status') == 1){
            if($general_settings != Null && $general_settings->auto_approve == 0){
                $driver_document = ProviderDocuments::query()
                    ->join('required_documents','required_documents.id','=','provider_documents.req_document_id')
                    ->where('required_documents.status', 1)
                    ->where('provider_documents.user_id', $request->get('user_id'));

                $driver_pending_document = (clone $driver_document)->where('provider_documents.status',0)->first();
                $driver_rejected_document = (clone $driver_document)->where('provider_documents.status',2)->first();
                $driver_expired_document = (clone $driver_document)->where('provider_documents.status',3)->first();
                if($driver_pending_document != Null){
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.370'),
                        "message_code" => 370,
                        "is_document_pending" => 1
                    ]);
                }
                if($driver_rejected_document != Null){
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.368'),
                        "message_code" => 370,
                        "is_document_pending" => 1
                    ]);
                }
                if($driver_expired_document != Null){
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.342'),
                        "message_code" => 342,
                        "is_document_expired" => 1
                    ]);
                }
            }

        }
        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
            $driver_details = TransportDriverDetails::query()->where('user_id', $user_details->id)->first();
            if ($driver_details != Null) {
                    $user_details->driver_current_status = $request->get('update_status');
                    $user_details->save();
                    if ($request->get('update_status') == 1) {
                        $driver_details->last_online_date_time = date('Y-m-d H:i:s');
                        $driver_details->save();
                    }
                    return response()->json([
                        "status" => 1,
                        "message" => __('driver_messages.1'),
                        "message_code" => 1,
                        "driver_current_status" => $user_details->driver_current_status - 0
                    ]);
//                }

            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }

    }

    public function postAvailableRideRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        try {
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }
        $user_details = User::query()->where("id", "=", $request->get('user_id'))->first();
        $user_currency = \App\Support\UserCurrencyResolver::forUser($user_details);
        $currency = $user_currency != null ? (float) $user_currency->ratio : 1.0;

        $language = $user_details->language;
        if ($language != "en" && $language != "" && $language != "Null") {
            $lang_prefix = $language . "_";
        } else {
            $lang_prefix = "";
        }

        $service_setting = ServiceSettings::query()->first();
        if ($service_setting != Null) {
            $radius = $service_setting->provider_search_radius;
            $timeout = $service_setting->provider_accept_timeout;
            $ride_expiry = $service_setting->ride_expiry;
            $nearest_ride_popup = $service_setting->nearest_ride_popup;
        }
        else {
            $radius = 5;
            $timeout = 60;
            $ride_expiry = 30;
            $nearest_ride_popup =  0.5;
        }

        $driver_details = TransportDriverDetails::query()
                        ->select('transport_driver_details.*','transport_vehicle_type.service_id')
                        ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
                        ->where('user_id',$request->get('user_id'))->first();
        if($driver_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('driver_messages.5'),
                'message_code' => 5,
            ]);
        }

        [$current_lat, $current_long] = \App\Support\DriverLocationHelper::syncFromRequest($request, $driver_details);
        $hasDriverGps = \App\Support\DriverLocationHelper::isValid((float) $current_lat, (float) $current_long);
        $effectiveRadius = \App\Helpers\ServiceCatalogHelper::effectiveDriverSearchRadiusKm($driver_details, (float) $radius);

        $deliveryCapable = ServiceCatalogHelper::driverCanReceiveDelivery(
            (int) ($driver_details->vehicle_type_id ?? 0),
            (int) ($driver_details->service_id ?? 0)
        );
        $canReceiveDelivery = $deliveryCapable && (int) ($driver_details->accept_delivery ?? 0) === 1;
        $canReceiveEncomiendas = $deliveryCapable
            && (int) ($driver_details->accept_encomiendas ?? ($driver_details->accept_delivery ?? 0)) === 1;
        $driverServiceId = (int) ($driver_details->service_id ?? 0);

        $allowedServiceModes = [];
        if ((int) ($driver_details->accept_transport ?? 1) === 1
            && in_array($driverServiceId, ServiceCatalogHelper::DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS, true)) {
            $allowedServiceModes[] = 'transport';
        }
        if ((int) ($driver_details->accept_transport ?? 1) === 1
            && $driverServiceId === 7
            && \App\Helpers\MobileFeatureFlagsHelper::isExpresoEnabled()) {
            $allowedServiceModes[] = 'viajes_compartidos';
        }
        if ($canReceiveDelivery) {
            $allowedServiceModes[] = 'delivery';
        }
        if ($canReceiveEncomiendas && \App\Helpers\MobileFeatureFlagsHelper::isEncomiendasEnabled()) {
            $allowedServiceModes[] = 'encomiendas';
        }
        if (\App\Helpers\AcarreoHelper::driverCanReceiveAcarreoRequests($driver_details)
            && \App\Helpers\MobileFeatureFlagsHelper::isAcarreosEnabled()) {
            $allowedServiceModes[] = 'acarreos';
        }
        if ($allowedServiceModes === []) {
            $allowedServiceModes[] = 'transport';
        }
        // Transport-category vehicle types (carro, moto, motoratón) always poll transport rides.
        if (in_array($driverServiceId, ServiceCatalogHelper::DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS, true)
            && !in_array('transport', $allowedServiceModes, true)) {
            $allowedServiceModes[] = 'transport';
        }
        $allowedServiceModes = array_values(array_unique($allowedServiceModes));

        $avatar = url('/assets/images/profile-images/customer/');
        $expire_date_time = date('Y-m-d H:i:s', strtotime("-".$ride_expiry." minutes"));

        $courierSelect = [
            'user_courier_service_details.recipient_name',
            'user_courier_service_details.recipient_contact_number',
            'user_courier_service_details.item_description',
            'user_courier_service_details.estimate_price',
            'user_courier_service_details.package_weight_kg',
            'user_courier_service_details.package_height_cm',
            'user_courier_service_details.package_width_cm',
            'user_courier_service_details.package_length_cm',
        ];
        if (Schema::hasColumn('user_courier_service_details', 'errand_type')) {
            $courierSelect[] = 'user_courier_service_details.errand_type';
        }
        $driverUserId = (int) $driver_details->user_id;

        $availableRideSelect = [
                    'user_ride_booking.id as ride_id','user_ride_booking.ride_no','user_ride_booking.pickup_address','user_ride_booking.destination_address','users.rating','user_ride_booking.pickup_datetime as schedule_date',
                    'user_ride_booking.user_name','user_ride_booking.pickup_lat','user_ride_booking.pickup_long','user_ride_booking.user_id','user_ride_booking.driver_id','user_ride_booking.vehicle_service_id as service_id','vehicle_services.service_mode','user_ride_booking.ride_type','user_ride_booking.is_auto_accept','user_ride_booking.destination_payment_method',
                    'vehicle_services.'.$lang_prefix.'name as service_name',
                    'user_ride_booking.child_seat','user_ride_booking.handicap','user_ride_booking.other_user_name','user_ride_booking.other_user_contact_number',
                    DB::raw('COALESCE(NULLIF(user_ride_booking.offered_price, 0), user_ride_booking.total_pay) * '.$currency.' as offered_price'),
                    DB::raw("COUNT(user_rating.id) as total_ratings "),'user_ride_booking.additional_request as additional_remarks',
//                                            DB::raw("TIMESTAMPDIFF(SECOND, user_ride_booking.created_at, NOW()) AS order_time"),
                    DB::raw("SUBSTRING_INDEX(user_ride_booking.destination_latlong,',',1) as destination_lat"),
                    DB::raw("SUBSTRING_INDEX(user_ride_booking.destination_latlong,',',-1) as destination_long"),
                    DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$avatar','/',users.avatar,'?v=0.4')) ELSE '' END) as profile_image"),
                    DB::raw("
                                                CASE WHEN (MINUTE(TIMEDIFF(user_ride_booking.created_at, NOW())) >= 1)
                                                THEN
                                                    CONCAT(MINUTE(TIMEDIFF(user_ride_booking.created_at, NOW())) ,'min ',SECOND(TIMEDIFF(user_ride_booking.created_at, NOW())),'sec ago')
                                                ELSE
                                                    CONCAT(TIMESTAMPDIFF(SECOND, user_ride_booking.created_at, NOW()),'sec ago')
                                                END
                                                AS order_time"),
                    DB::raw("ROUND((6371 * acos( cos( radians(pickup_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(pickup_long) ) + sin( radians(pickup_lat) ) * sin(radians( " .$current_lat. " ) ) ) ), 2) as distance" ),
                    DB::raw("ROUND((((6371 * acos( cos( radians(pickup_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(pickup_long) ) + sin( radians(pickup_lat) ) * sin(radians( " .$current_lat. " ) ) ) ) / 40 ) * 60 ), 2) as time" ),
                ];
        if (Schema::hasColumn('user_ride_booking', 'delivery_variant')) {
            $availableRideSelect[] = 'user_ride_booking.delivery_variant';
        }

            $available_ride_requests = TransportRideBook::query()
                ->select(array_merge($availableRideSelect, $courierSelect))
                ->join('users','users.id','=','user_ride_booking.user_id')
                ->leftjoin('user_courier_service_details','user_courier_service_details.ride_id','=','user_ride_booking.id')
                ->leftJoin('user_rating','user_rating.user_id','=','users.id')
                ->where(function ($assignedDriverScope) use ($driverUserId) {
                    $assignedDriverScope->whereNull('user_ride_booking.driver_id')
                        ->orWhere('user_ride_booking.driver_id', 0)
                        ->orWhere('user_ride_booking.driver_id', $driverUserId);
                })
                ->where(function ($proximityScope) use ($hasDriverGps, $current_lat, $current_long, $effectiveRadius, $driverUserId) {
                    $proximityScope->where('user_ride_booking.driver_id', $driverUserId);
                    if ($hasDriverGps) {
                        $proximityScope->orWhereRaw(DB::raw("(6371 * acos( cos( radians(pickup_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(pickup_long) ) + sin( radians(pickup_lat) ) * sin(radians( " .$current_lat. " ) ) ) ) < " . $effectiveRadius));
                    } else {
                        $proximityScope->orWhere(function ($unassignedOnly) {
                            $unassignedOnly->whereNull('user_ride_booking.driver_id')
                                ->orWhere('user_ride_booking.driver_id', 0);
                        });
                    }
                })
                ->where('user_ride_booking.status',0)
                ->where('users.status',1)
                ->where('users.id','!=',$driver_details->user_id)
                ->join('vehicle_services', 'vehicle_services.id', '=', 'user_ride_booking.vehicle_service_id');
        $available_ride_requests = ServiceCatalogHelper::applyDriverAvailableRidesServiceFilter(
            $available_ride_requests,
            $driver_details
        );
        if (Schema::hasColumn('vehicle_services', 'service_mode')) {
            $modeFilter = array_values(array_filter(
                $allowedServiceModes,
                static fn ($m) => $m !== 'encomiendas'
            ));
            $modesForQuery = $modeFilter;
            if (in_array('viajes_compartidos', $modeFilter, true)) {
                $modesForQuery[] = 'expreso';
            }
            $modesForQuery = array_values(array_unique($modesForQuery));
            $available_ride_requests = $available_ride_requests->where(function ($modeOuter) use ($modesForQuery, $modeFilter, $canReceiveEncomiendas) {
                if ($modesForQuery !== []) {
                    $modeOuter->whereIn('vehicle_services.service_mode', $modesForQuery);
                }
                if ($canReceiveEncomiendas && Schema::hasColumn('user_courier_service_details', 'errand_type')) {
                    $method = $modeFilter !== [] ? 'orWhere' : 'where';
                    $modeOuter->{$method}(function ($encomiendaOnly) {
                        $encomiendaOnly->where('user_courier_service_details.errand_type', \App\Helpers\EncomiendaHelper::ERRAND_ENCOMIENDA);
                    });
                }
            });
        }

        $available_ride_requests = $available_ride_requests
                ->where('user_ride_booking.ride_time_out', '>=', $expire_date_time)
                ->whereNull('users.deleted_at')
                ->when($driver_details->child_seat != 1, function ($q) {
                    $q->where('user_ride_booking.child_seat', 0);
                })
                ->when($driver_details->handicap != 1, function ($q) {
                    $q->where('user_ride_booking.handicap', 0);
                })
                ->orderBy('distance','ASC')
                ->groupBy('user_ride_booking.id');

        $available_ride_requests = $available_ride_requests->get()->toArray();

        $address_list = array();
        foreach ($available_ride_requests as $key => $value){
            $address_list = [];
            $address_list[] = [
                "address" => $value['pickup_address'],
                "address_lat" => trim($value['pickup_lat']),
                "address_long" => trim($value['pickup_long'])
            ];
            unset($available_ride_requests[$key]['driver_id']);
            $ride_way_point = UserRideWayPoint::query()->where('ride_id', $value['ride_id'])->first();
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
            $address_list[] = [
                "address" => $value['destination_address'],
                "address_lat" => trim($value['destination_lat']),
                "address_long" => trim($value['destination_long'])
            ];
            $address_list = array_values(array_filter($address_list, function ($entry) {
                $lat = (float) ($entry['address_lat'] ?? 0);
                $lng = (float) ($entry['address_long'] ?? 0);

                return abs($lat) >= 0.01 && abs($lng) >= 0.01;
            }));
            $available_ride_requests[$key]["address_list"] = $address_list ?? [];
            $available_ride_requests[$key]['destination_payment_label'] = DestinationPaymentHelper::label(
                $value['destination_payment_method'] ?? null,
                $language ?? 'en'
            );
            $available_ride_requests[$key]['is_delivery'] = RideKindHelper::isDeliveryFlag($value);
            $available_ride_requests[$key]['is_encomienda'] = \App\Helpers\EncomiendaHelper::isEncomiendaFlag($value);
            $address_list = [];
        }

        $services = VehicleService::query()->select('id as service_id',$lang_prefix.'name as service_name',
            DB::raw('cost_for_km * ' . $currency . ' as cost_for_km'),
            DB::raw('max_bargain_percent as min_offer_fare_amount'))
            ->where('id',$driver_details->service_id)
            ->where('status', 1)->first();

        $general_settings = request()->get("general_settings");
        return response()->json([
            "status" => 1,
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "ride_list" => $available_ride_requests,
            "service" => $services,
            "nearest_ride_popup" => $nearest_ride_popup,
            "driver_price_suggestion" => $general_settings?->driver_price_suggestion ?? 1
        ]);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                "ride_list" => [],
                "service" => null,
                "nearest_ride_popup" => 0.5,
                "driver_price_suggestion" => 1,
            ]);
        }
    }

    public function postDriverBid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "offered_price" => "required|numeric",
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

        $driver_details = TransportDriverDetails::query()
            ->select('transport_driver_details.*','transport_vehicle_type.service_id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
            ->where('transport_driver_details.user_id',$request->get('user_id'))->first();
        if($driver_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('driver_messages.5'),
                'message_code' => 5,
            ]);
        }

        $ride_details = TransportRideBook::query()->select('user_ride_booking.*','user_courier_service_details.recipient_name','user_courier_service_details.recipient_contact_number','user_courier_service_details.item_description','user_courier_service_details.estimate_price')
                ->leftjoin('user_courier_service_details','user_courier_service_details.ride_id','=','user_ride_booking.id')
                ->where('user_ride_booking.id',$request->get('ride_id'))->first();

        if ($ride_details == Null) {
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }

        if($ride_details->status != 0){
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.23'),
                "message_code" => 23,
            ]);
        }

        if ((int) ($ride_details->is_auto_accept ?? 0) === 1) {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.23'),
                "message_code" => 23,
            ]);
        }

        if (! \App\Helpers\RideDriverEligibilityHelper::driverCanServePendingRide($driver_details, (int) $request->get('ride_id'))) {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.343'),
                "message_code" => 343,
            ]);
        }

        //check driver is not accept any schedule ride in current schedule ride time periods
        if($ride_details->ride_type == 1) {
            $pickup_datetime = $ride_details->pickup_datetime;
            $destination_datetime = $ride_details->destination_datetime;
            $driver_past_ride = TransportRideBook::query()
                ->select('id','driver_id','pickup_datetime','destination_datetime','status')
                ->where('status', 2)
                ->where('driver_id', $request->get('user_id'))
                ->Where(function ($query) use ($pickup_datetime, $destination_datetime) {
                    $query->whereBetween('pickup_datetime', [$pickup_datetime, $destination_datetime]);
                    $query->orwhereBetween('destination_datetime', [$pickup_datetime, $destination_datetime]);
                    $query->orWhere(function ($query) use ($pickup_datetime, $destination_datetime) {
                        $query->where('pickup_datetime', '<=', $pickup_datetime)
                            ->where('destination_datetime', '>=', $destination_datetime);
                    });
                })
                ->first();
            if($driver_past_ride != Null){
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.367'),
                    "message_code" => 367,
                ]);
            }
        }

        $passenger_details = User::query()->where('id',$ride_details->user_id,'currency')->where('status',1)->whereNull('deleted_at')->first();
        if($passenger_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }
      $general_settings=request()->get('general_settings');
        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_check);

        $driver_bid_accepted = DriverBid::query()->where('ride_id',$request->get('ride_id'))->where('status',1)->first();
        $service_setting = ServiceSettings::query()->select('admin_commission','driver_timeout')->first();
        if($general_settings->auto_settle_wallet == 1){
            //get wallet balance
            $last_amount = $this->notificationClass->getWalletBalance($request->get('user_id'));
            $commissionPercent = VehicleCommissionHelper::resolvePercent(
                (int) $ride_details->vehicle_service_id,
                $ride_details->delivery_variant ?? null
            );
            $commissionRate = $commissionPercent / 100;
            $vatRate = ((float) ($general_settings->vat_rate_on_commission ?? 19)) / 100;
            $commissionFactor = $commissionRate * (1 + $vatRate);
            $admin_commission_driver_offered = (($request->get('offered_price') / $currency) * $commissionFactor);
            $admin_commission_user_offered = ($ride_details->offered_price * $commissionFactor);
            $requiredCommission = max($admin_commission_driver_offered, $admin_commission_user_offered);
            if ($last_amount < $requiredCommission) {
                $amount = $requiredCommission;
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.339',['amount' => round($amount*$currency,2),'currency_code' => $user_currency->symbol]),
                    "message_code" => 339
                ]);
            }
        }

        if($driver_bid_accepted != Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.325'),
                "message_code" => 325,
            ]);
        }else{
            $driver_bid = DriverBid::query()->where('ride_id',$request->get('ride_id'))->where('driver_id',$driver_details->user_id)->first();
            if($driver_bid == Null){
                $driver_bid = new DriverBid();
                $driver_bid->driver_id = $driver_details->user_id;
                $driver_bid->user_id = $ride_details->user_id;
                $driver_bid->ride_id = $ride_details->id;
            }
            $driver_bid->vehicle_type_id = $driver_details->vehicle_type_id;
            $driver_bid->offered_price = TripAmountHelper::parseDisplayAmount(
                $request->get('offered_price'),
                (string) (\App\Support\UserCurrencyResolver::forUser($user_check)?->currency_code
                    ?? \App\Support\UserCurrencyResolver::forUser($user_check)?->symbol
                    ?? '')
            ) / $currency;
            $driver_bid->status = 0;
            $driver_bid->bidding_time = date('Y-m-d H:i:s');
            $driver_bid->save();
        }
        $this->notificationClass->userBidNotification($ride_details,$passenger_details->device_token,$passenger_details->language,$passenger_details->login_device);
        return response()->json([
            "status" => 1,
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "timeout" =>$service_setting->driver_timeout != Null ? $service_setting->driver_timeout : '',
            "user_profile" => $passenger_details->avatar != Null ? url('/assets/images/profile-images/customer/'.$passenger_details->avatar) : '',
        ]);
    }

    public function postDriverBidList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
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

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        $ride_details = TransportRideBook::query()->where('id',$request->get('ride_id'))->where('status',0)->first();
        if($ride_details == Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }

        $service_setting = ServiceSettings::query()->first();
        if ($service_setting != Null) {
            $timeout = $service_setting->driver_timeout;
        }
        else {
            $timeout = 30;
        }

        $pickup_lat = $ride_details->pickup_lat;
        $pickup_long = $ride_details->pickup_long;

        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_details);

        $avatar = url('/assets/images/profile-images/customer/');
        $vehicle_service_icon = url('/assets/images/vehicle-service/');
        $expire_date_time = date('Y-m-d H:i:s', strtotime("-".$timeout." seconds"));
        $driver_bid_list = DriverBid::query()
            ->select(
                'driver_ride_bid_amount.ride_id', 'driver_ride_bid_amount.driver_id', 'driver_ride_bid_amount.user_id',
                DB::raw('driver_ride_bid_amount.offered_price * ' . $currency . ' as offered_price'),
                'users.first_name as user_name', 'transport_driver_details.rating', 'transport_driver_details.vehicle_company', 'transport_driver_details.model_name', 'transport_driver_details.is_taxi', 'transport_vehicle_type.name as vehicle_type_name',
                'vehicle_services.id as service_id',
                'user_ride_booking.ride_type',
                DB::raw("COUNT(transport_driver_rating.id) as total_ratings "),
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$avatar','/',users.avatar,'?v=0.4')) ELSE '' END) as profile_image"),
                DB::raw("(CASE WHEN vehicle_services.vehicle_service_icon != '' THEN (concat('$vehicle_service_icon','/',vehicle_services.vehicle_service_icon)) ELSE '' END) as vehicle_service_icon"),'driver_ride_bid_amount.bidding_time',
                DB::raw("ROUND((6371 * acos( cos( radians(" . $pickup_lat . ") ) * cos( radians(current_lat) )  * cos( radians( transport_driver_details.current_long ) - radians(" . $pickup_long . ") ) + sin( radians(current_lat) ) * sin(radians( " . $pickup_lat . " ) ) ) ), 2) as distance"),
                DB::raw("ROUND((((6371 * acos( cos( radians(" . $pickup_lat . ") ) * cos( radians(current_lat) )  * cos( radians( transport_driver_details.current_long ) - radians(" . $pickup_long . ") ) + sin( radians(current_lat) ) * sin(radians(" . $pickup_lat . ") ) ) ) / 40 ) * 60 ), 2) as time")
            )
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'driver_ride_bid_amount.vehicle_type_id')
            ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
            ->join('transport_driver_details', 'transport_driver_details.user_id', '=', 'driver_ride_bid_amount.driver_id')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->join('user_ride_booking','user_ride_booking.id','=','driver_ride_bid_amount.ride_id')
            ->leftjoin('transport_driver_rating', 'transport_driver_rating.driver_id', '=', 'users.id')
            ->where('driver_ride_bid_amount.ride_id', $request->get('ride_id'))
            ->where('driver_ride_bid_amount.status', 0)
            ->where('driver_ride_bid_amount.bidding_time', '>=', $expire_date_time)
            ->where('users.is_driver_status', 1)
            ->where('users.driver_current_status', 1)
            ->groupBy('driver_ride_bid_amount.id')
            ->get()->toArray();

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "driver_bid_list" => $driver_bid_list,
            "time_out" => $timeout
        ]);

    }

    public function postUpdatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "offered_price" => "required|numeric",
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

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        $ride_details = TransportRideBook::query()->where('id',$request->get('ride_id'))->where('status',0)->first();
        if($ride_details == Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }

        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_details);
        $currencyMeta = \App\Support\UserCurrencyResolver::forUser($user_details);
        $currencyLabel = (string) ($currencyMeta->currency_code ?? $currencyMeta->symbol ?? '');

        $displayOffered = TripAmountHelper::parseDisplayAmount($request->get('offered_price'), $currencyLabel);
        $amount = round($displayOffered / $currency, 2);

        $date = new \DateTime("now", new \DateTimeZone(config('app.timezone')) );

        if (abs((float) $ride_details->offered_price * $currency - $displayOffered) > 0.009) {
            $ride_details->total_pay = $amount;
            $ride_details->offered_price = $amount;
            $ride_details->ride_time_out = $date->format('Y-m-d H:i:s');
            $ride_details->save();
            DriverBid::query()->where('ride_id',$request->get('ride_id'))->update(['status' => 2]);
            $this->notificationClass->userFareChangeNotification($request->get('ride_id'));
        }

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "offered_price" => $ride_details->offered_price
        ]);

    }

    public function postDeclineRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "driver_id" => "required|numeric",
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

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        $driver_bid = DriverBid::query()->where('ride_id',$request->get('ride_id'))->where('driver_id',$request->get('driver_id'))->first();
        if($driver_bid == Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.326'),
                "message_code" => 326,
            ]);
        }
        $driver_bid->status = 2;
        $driver_bid->save();

        $driver = User::query()->select('device_token','language','login_device')->where('id',$request->get('driver_id'))->first();
        if ($driver != Null) {
            try {
                $this->notificationClass->driverBidRejectNotification($driver->device_token, $driver->language, (int) $driver->login_device);
            } catch (\Throwable $e) {
                \Log::warning('postDeclineRequest: push notification failed', [
                    'ride_id' => $request->get('ride_id'),
                    'driver_id' => $request->get('driver_id'),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
        ]);

    }

    public function postGetRideStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
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

        $user_details = User::query()->where('id',$request->get('user_id'))->where('status',1)->whereNull('deleted_at')->first();
        if($user_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        $ride_details = TransportRideBook::query()->where('id',$request->get('ride_id'))->first();
        if($ride_details == Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.26'),
                "message_code" => 26,
            ]);
        }

        $driver_bid_accepted = DriverBid::query()->where('ride_id',$request->get('ride_id'))->where('driver_id','!=',$user_details->id)->where('status',1)->first();
        if($driver_bid_accepted != Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.325'),
                "message_code" => 325,
            ]);
        }

        $driver_bid = DriverBid::query()->where('ride_id',$request->get('ride_id'))->where('driver_id',$user_details->id)->first();
        if($driver_bid == Null){
            return response()->json([
                "status" => 0,
                "message" => __('user_messages.326'),
                "message_code" => 326,
            ]);
        }else{
            return response()->json([
                "status" => 1,
                "message" => __('user_messages.1'),
                "message_code" => 1,
                "bid_status" => $driver_bid->status,
                "ride_status" => $ride_details->status,
                "ride_type" => $ride_details->ride_type,
                "driver_can_cancel" => AppMobileSettingsHelper::driverCanCancelRide((int) $ride_details->status),
            ]);
        }

    }

    public function postCancelRide(Request $request)
    {
        return $this->cancelTransportRide($request);
    }

    public function postAcceptRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "driver_id" => "required|numeric",
            "ride_id" => "required|numeric",
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
        $driver_id = $request->get('driver_id');
        $ride = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();
        if ($ride != Null) {
            if ($ride->status == 0) {
                return $this->notificationClass->driverAcceptedTransportRequestNotification($request->get('ride_id'), $driver_id);
            } elseif ($ride->status == 4) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.24'),
                    "message_code" => 24,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.23'),
                    "message_code" => 23,
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    public function postDriverGetRunningService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }
        $driver_id = $request->get('user_id');
        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $settings = request()->get("general_settings");
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        RideSessionHelper::reconcileForUser((int) $driver_id, $this->notificationClass);

            $get_running_service = ProviderUserRunningService::query()->where('provider_id', $driver_id)->get();
            if ($get_running_service->isNotEmpty()) {
                $running_ride = [];
                foreach ($get_running_service as $running_services) {
                    $ride_details = TransportRideBook::query()
                                    ->select('user_ride_booking.id as ride_id','user_ride_booking.status as ride_status')
                                    ->where('user_ride_booking.id', $running_services->booking_id)
                                    ->first();
                    if ($ride_details != Null) {
                        $status = (int) $ride_details->ride_status;
                        if (! in_array($status, RideSessionHelper::TERMINAL_STATUSES, true)) {
                            $running_ride[] = [
                                "ride_id" => $ride_details->ride_id,
                                "ride_status" => $ride_details->ride_status,
                            ];
                        }
                    }
                }
                if (!empty($running_ride)){
                    return response()->json([
                        "status" => 1,
                        "message" => __('driver_messages.1'),
                        "message_code" => 1,
                        "running_rides" => $running_ride,
                        "is_auto_settle" => $settings->auto_settle_wallet,
                    ]);
                }else{
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.1'),
                        "message_code" => 1,
                        "is_auto_settle" => $settings->auto_settle_wallet,
                    ]);
                }
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.1'),
                    "message_code" => 1,
                    "is_auto_settle" => $settings->auto_settle_wallet,
                ]);
            }

    }

    public function postDriverUpdateRideStatus(Request $request)
    {
        $this->notificationClass->ApiLogDetail($logger_type = 2, $request->get('driver_id'), "postDriverUpdateRideStatus", $request->all());

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
            "ride_status" => "required|numeric|in:3,4,5,6,7,8,9",
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

        $driver_id = $request->get('user_id');
        $request_status = $request->get('ride_status');
        $way_point_status = $request->get('way_point_status');
        $general_settings = request()->get("general_settings");

        $cancel_message = "";
            $ride = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();
            if ($ride != Null) {
                $ride_status = $ride->status;
                $driver_details = TransportDriverDetails::query()->select('transport_driver_details.user_id', 'users.status', 'users.driver_current_status as current_status')
                    ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
                    ->where('transport_driver_details.user_id', $request->get('user_id'))
                    ->first();
                if ($driver_details == Null) {
                    return response()->json([
                        "status" => 0,
//                        "message" => "driver details not found!",
                        "message" => __('driver_messages.15'),
                        "message_code" => 15,
                    ]);
                }
                if ($ride->is_hail != 1) {
                    $user = User::query()->where('id', $ride->user_id)->first();
                    if ($user == Null) {
                        return response()->json([
                            "status" => 0,
//                        "message" => "something went to wrong!",
                            "message" => __('driver_messages.9'),
                            "message_code" => 9,
                        ]);
                    }
                }
                if ($driver_details->user_id != $ride->driver_id){
                    $reassigned_driver_detail = null;
                    if (Schema::hasTable('reassigned_driver_list')) {
                        $reassigned_driver_detail = DB::table('reassigned_driver_list')
                            ->where('ride_id', $ride->id)
                            ->where('driver_id', $driver_details->user_id)
                            ->first();
                    }
                    if ($reassigned_driver_detail != Null){
                        return response()->json([
                            "status" => 0,
                            "message" => __('driver_messages.270'),
                            "message_code" => 270,
                            "admin_reassign" => 1,
                        ]);
                    } else {
                        return response()->json([
                            "status" => 0,
                            //"message" => "something went to wrong!",
                            "message" => __('driver_messages.9'),
                            "message_code" => 9,
                        ]);
                    }
                }
                if ($ride_status > 0) {
                    if ($ride_status == 9) {
                        $update_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                        if ($update_driver_status != Null) {
                            $update_driver_status->driver_current_status = 1;
                            $update_driver_status->save();
                        } else {
                            return response()->json([
                                "status" => 0,
//                                "message" => "something went to wrong!",
                                "message" => __('driver_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                        //ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('service_cat_id', $service_category_id)->where('booking_id', $ride->id)->delete();
                        return response()->json([
                            "status" => 1,
//                            "message" => "success!",
                            "message" => __('driver_messages.1'),
                            "message_code" => 1,
                            "ride_id" => $ride->id,
                            "ride_status" => $request_status - 0,
                            "ride_completed_status" => 1,
                            "ride_cancelled_status" => 0,
                            "driver_current_status" => $update_driver_status->driver_current_status - 0,
                            "cash_payment" => $general_settings->cash_payment != NUll ? $general_settings->cash_payment: 0,
                            "card_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
                            "wallet_payment" => $general_settings->wallet_payment != NUll ? $general_settings->wallet_payment: 0,
                            "cancel_by" => $cancel_message,
                            "way_point_status" => $way_point_status - 0,
                        ]);
                    } elseif ($ride_status == 4) {
                        $update_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                        if ($update_driver_status != Null) {
                            $update_driver_status->driver_current_status = 1;
                            $update_driver_status->save();
                        } else {
                            return response()->json([
                                "status" => 0,
                                //"message" => "something went to wrong!",
                                "message" => __('driver_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                        $cancel_by = $ride->cancel_by;
                        if ($cancel_by != Null && strtolower($cancel_by) == "admin") {
//                            $cancel_message = "Ride Cancelled by Admin";
                            $cancel_message = __('driver_messages.113');
                            $message_code = 113;
                        } elseif ($cancel_by != Null && strtolower($cancel_by) == "driver") {
                            $message_code = 20;
//                            $cancel_message = "Driver cancel the ride.";
                            $cancel_message = __('driver_messages.20');
                        } else {
//                            $cancel_message = "Ride Cancelled by User";
                            $cancel_message = __('driver_messages.24');
                            $message_code = 24;
                        }

                        //deleting chat from firebase
                        (new FirebaseService())->deleteOrderChat($ride->ride_no,$ride->id);

                        //ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('service_cat_id', $service_category_id)->where('booking_id', $ride->id)->delete();
                        return response()->json([
                            "status" => 1,
//                            "message" => "success!",
                            "message" => __('driver_messages.1'),
                            "message_code" => 1,
                            "ride_id" => $ride->id,
                            "ride_status" => $request_status - 0,
                            "ride_completed_status" => 0,
                            "ride_cancelled_status" => 1,
                            "driver_current_status" => $update_driver_status->driver_current_status - 0,
                            "cancel_by" => $cancel_message,
                            "way_point_status" => $way_point_status - 0,
                        ]);
                    } elseif ($ride_status == 7 && $ride->completed_by == 1) {
                        if ($ride->payment_type == 2 || $ride->payment_type == 3) {
                            if ($ride->payment_status != 1)
                                return response()->json([
                                    "status" => 0,
//                                "message" => "Payment Process Pending",
                                    "message" => __('driver_messages.83'),
                                    "message_code" => 83,
                                ]);
                        }
                        $update_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                        if ($update_driver_status != Null) {
                            $update_driver_status->driver_current_status = 1;
                            $update_driver_status->save();
                        } else {
                            return response()->json([
                                "status" => 0,
//                                "message" => "something went to wrong!",
                                "message" => __('driver_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                        ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                        return response()->json([
                            "status" => 1,
//                            "message" => "success!",
                            "message" => __('driver_messages.1'),
                            "message_code" => 1,
                            "ride_id" => $ride->id,
                            "ride_status" => $request_status - 0,
                            "ride_completed_status" => 1,
                            "ride_cancelled_status" => 0,
                            "driver_current_status" => $update_driver_status->driver_current_status - 0,
                            "cancel_by" => $cancel_message,
                            "way_point_status" => $way_point_status - 0,
                        ]);
                    } else {
                        if ($request_status == 3) {
                            if ($ride_status == 1) {
                                $change_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                                if ($change_driver_status == Null) {
                                    return response()->json([
                                        "status" => 0,
                                        "message" => __('driver_messages.9'),
                                        "message_code" => 9,
                                    ]);
                                }
                                $change_driver_status->driver_current_status = 0;
                                $change_driver_status->save();
                                ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                                $provider_running_service = new ProviderUserRunningService();
                                $provider_running_service->provider_id = $driver_id;
                                $provider_running_service->user_id = $ride->user_id;
                                $provider_running_service->booking_id = $ride->id;
                                $provider_running_service->save();

                                $user_running_ride = UserRunningRide::query()->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->first();
                                if ($user_running_ride == Null) {
                                    $user_running_ride = new UserRunningRide();
                                    $user_running_ride->user_id = $ride->user_id;
                                    $user_running_ride->booking_id = $ride->id;
                                    $user_running_ride->save();
                                }
                                $ride->status = $request_status;
                                $ride->save();
                                $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "otp" => $ride->otp,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $driver_details->current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                    "driver_can_cancel" => AppMobileSettingsHelper::driverCanCancelRide((int) $request_status),
                                ]);
                            } elseif ($ride_status == 2) {
                                $pickup_date_time = strtotime(date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($ride->pickup_datetime))));
                                $current_date_time = strtotime(date('Y-m-d H:i:s'));
                                if ($current_date_time < $pickup_date_time) {
                                    return response()->json([
                                        "status" => 0,
                                        "message" => __('driver_messages.118'),
                                        "message_code" => 118,
                                    ]);
                                }
                                $change_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                                if ($change_driver_status == Null) {
                                    return response()->json([
                                        "status" => 0,
//                                            "message" => "something went to wrong!",
                                        "message" => __('driver_messages.9'),
                                        "message_code" => 9,
                                    ]);
                                }
                                $change_driver_status->driver_current_status = 0;
                                $change_driver_status->save();
                                ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                                $provider_running_service = new ProviderUserRunningService();
                                $provider_running_service->provider_id = $driver_id;
                                $provider_running_service->user_id = $ride->user_id;
                                $provider_running_service->booking_id = $ride->id;
                                $provider_running_service->save();

                                $user_running_ride = UserRunningRide::query()->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->first();
                                if ($user_running_ride == Null) {
                                    $user_running_ride = new UserRunningRide();
                                    $user_running_ride->user_id = $ride->user_id;
                                    $user_running_ride->booking_id = $ride->id;
                                    $user_running_ride->save();
                                }
                                $ride->status = $request_status;
                                $ride->save();
                                $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "otp" => $ride->otp,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $driver_details->current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                    "driver_can_cancel" => AppMobileSettingsHelper::driverCanCancelRide((int) $request_status),
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } elseif ($request_status == 4) {
                            if (in_array($ride_status, [0, 1, 2, 3])) {
                                $validator = Validator::make($request->all(), [
                                    "cancel_reason" => "required",
                                ]);
                                if ($validator->fails()) {
                                    return response()->json([
                                        "status" => 0,
                                        "message" => $validator->errors()->first(),
                                        "message_code" => 9,
                                    ]);
                                }
                                $update_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                                if ($update_driver_status == Null) {
                                    return response()->json([
                                        "status" => 0,
//                                        "message" => "something went to wrong!",
                                        "message" => __('driver_messages.9'),
                                        "message_code" => 9,
                                    ]);
                                }
                                $ride_status = $ride->status;
                                $ride->otp = null;
                                $ride->status = $request_status;
                                $ride->cancel_by = "driver";
                                $ride->cancel_reason = $request->get('cancel_reason');
                                $ride->save();

                                //deleting chat from firebase
                                if((new FirebaseService())->deleteOrderChat($ride->ride_no,$ride->id)){
                                }

                                $update_driver_status->driver_current_status = 1;
                                $update_driver_status->save();

                                if ($ride_status < 6) {
                                    if ($ride->promo_code > 0) {
                                        $used_promocode_details = UsedPromocodeDetails::query()->where('id', $ride->promo_code)->first();
                                        if ($used_promocode_details != Null) {
                                            $get_promocode = PromocodeDetails::query()->where('id', $used_promocode_details->promocode_id)->first();
                                            if ($get_promocode != Null) {
                                                $count_promocode = $get_promocode->total_usage - 1;
                                                $get_promocode->total_usage = ($count_promocode > 0) ? $count_promocode : 0;
                                                $get_promocode->save();
                                            }
                                            $used_promocode_details->status = 2;
                                            $used_promocode_details->save();
                                        }
                                    }
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
                                //refer history code
                                $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                                UserRunningRide::query()->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();

                                $general_settings = request()->get("general_settings");
                                if ($general_settings != Null) {
                                    if ($general_settings->send_mail == 1) {
                                            $email = $user->email;
                                            $driver_name = ucwords(strtolower($driver_check['first_name'] . " " . $driver_check['last_name']));
                                            try {
                                                $mail_type = "your_request_rejected/canceled";
                                                $to_mail = $email;
                                                $subject = "Your " . $general_settings->mail_site_name . " Ride request Rejected/Canceled By " . $driver_name;
                                                $disp_data = array("##user_name##" => $ride->user_name,"##driver_name##" => $driver_name);

                                                $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                            } catch (\Exception $e) {
                                            }
                                    }
                                }
                                return response()->json([
                                    "status" => 1,
                                    //"message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $update_driver_status->driver_current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } elseif ($request_status == 5  && in_array($way_point_status, [0, 1, 2, 3])) {
                            if ($ride_status == 4 || $ride_status == 10) {
                                return response()->json([
                                    "status" => 0,
                                    //"message" => "ride cancelled!",
                                    "message" => __('driver_messages.30'),
                                    "message_code" => 30,
                                ]);
                            } elseif ($ride_status >= 3 && $ride_status < 6) {
                                //check code for ride otp verificatino
                                if ($ride->otp != Null) {
                                    $validator = Validator::make($request->all(), [
                                        "otp" => "nullable|numeric|digits:4"
                                    ]);
                                    if ($validator->fails()) {
                                        return response()->json([
                                            "status" => 0,
                                            "message" => $validator->errors()->first(),
                                            "message_code" => 9,
                                        ]);
                                    }
                                    if($general_settings->ride_otp == 1) {
                                        if ($ride->otp != $request->get('otp')) {
                                            return response()->json([
                                                "status" => 0,
                                                //"message" => "Invalid otp!",
                                                "message" => __('driver_messages.89'),
                                                "message_code" => 89,
                                            ]);
                                        }
                                    }
                                    $ride->otp = Null;
                                    $ride->save();
                                }
                                //End check code for ride otp verificatino

                                if ($ride_status != 5) {
                                    $ride->status = $request_status;
                                    $ride->save();
                                    $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                }

                                if ($ride->is_way_point == 1) {
                                    $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride->id)->first();
                                    if ($ride_way_point == Null) {
                                        return response()->json([
                                            "status" => 0,
//                                            "message" => "something went to wrong",
                                            "message" => __('driver_messages.9'),
                                            "message_code" => 9,
                                        ]);
                                    }
                                    $count_ride_way_point = 1;
                                    if ($ride_way_point->way_point_2 != Null && $ride_way_point->lat_long_2 != Null) {
                                        $count_ride_way_point = $count_ride_way_point + 1;
                                    }
                                    if ($ride_way_point->way_point_3 != Null && $ride_way_point->lat_long_3 != Null) {
                                        $count_ride_way_point = $count_ride_way_point + 1;
                                    }
                                    if ($way_point_status <= $count_ride_way_point && $way_point_status > $ride->way_point_status) {
                                        $ride->way_point_status = $way_point_status;
                                        $ride->save();
                                        if ($user->login_device != null) {
                                            //code for only send notification to fullter for send multiway point notification
                                            $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                        }
                                    }
                                }

                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $driver_details->current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $ride->way_point_status - 0,
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }

                        } elseif ($request_status == 6) {
                            if ($ride_status == 4 || $ride_status == 10) {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "ride cancelled!",
                                    "message" => __('driver_messages.30'),
                                    "message_code" => 30,
                                ]);
                            } elseif ($ride_status == 5) {
//                                $validator = Validator::make($request->all(), [
//                                    "route_lat_long_list" => "required",
//                                ]);
//                                if ($validator->fails()) {
//                                    return response()->json([
//                                        "status" => 0,
//                                        "message" => $validator->errors()->first(),
//                                        "message_code" => 9,
//                                    ]);
//                                }

                                //code for dynamic Toll charge module 0 - off , 1 - driver will give the final charge , 2 - driver will give no of tolls & charge per toll is decided by admin
                                if($general_settings != Null && $general_settings->is_toll_module > 0){
                                    $toll_charge = 0;
                                    $no_of_toll = 0;
                                    if($general_settings->is_toll_module == 1){
                                        $validator = Validator::make($request->all(), [
                                            "toll_charge" => "required|numeric",
                                        ]);
                                        if ($validator->fails()) {
                                            return response()->json([
                                                "status" => 0,
                                                "message" => $validator->errors()->first(),
                                                "message_code" => 9,
                                            ]);
                                        }
                                        $toll_charge = $request->get('toll_charge');//final toll charge
                                    } else if($general_settings->is_toll_module == 2){
                                        $validator = Validator::make($request->all(), [
                                            "no_of_toll" => "required|numeric"
                                        ]);
                                        if ($validator->fails()) {
                                            return response()->json([
                                                "status" => 0,
                                                "message" => $validator->errors()->first(),
                                                "message_code" => 9,
                                            ]);
                                        }
                                        $no_of_toll = $request->get('no_of_toll');//no of tolls
                                        $admin_charge_per_toll = $general_settings->charge_per_toll ? $general_settings->charge_per_toll : 0;//charge per toll set by admin
                                        $toll_charge = round($no_of_toll * $admin_charge_per_toll,2);//final toll charge
                                    } else{
                                        return response()->json([
                                            "status" => 0,
//                                            "message" => "Please try again",
                                            "message" => __('driver_messages.9'),
                                            "message_code" => 9,
                                        ]);
                                    }
                                    $driver_currency = \App\Support\UserCurrencyResolver::forCurrency($driver_check->currency);
                                    if ($driver_currency == Null) {
                                        $driver_currency = WorldCurrency::query()->where('default_currency', 1)->first();
                                    }

                                    $toll_charge = ($general_settings->is_toll_module == 2 ? $toll_charge : round(($toll_charge / $driver_currency->ratio), 2));
                                    $ride->no_of_toll = $no_of_toll;
                                    $ride->toll_charge = $toll_charge;
                                    $ride->total_pay = round($ride->total_pay + $toll_charge,2);
                                    $ride->driver_amount = round($ride->driver_amount + $toll_charge, 2);
                                    $ride->save();
                                }

                                if ($ride->promo_code > 0) {
                                    $used_promocode_details = UsedPromocodeDetails::query()->where('id', $ride->promo_code)->first();
                                    if ($used_promocode_details != Null) {
                                        $used_promocode_details->status = 1;
                                        $used_promocode_details->save();
                                    }
                                }

                                //change ride status
                                $ride->status = $request_status;
                                $ride->save();
                                if ($ride->is_hail != 1) {
                                    $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                }

                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $driver_details->current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } elseif ($request_status == 7) {
                            if ($ride_status == 4 || $ride_status == 10) {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "ride cancelled!",
                                    "message" => __('driver_messages.30'),
                                    "message_code" => 30,
                                ]);
                            } elseif ($ride_status == 6) {
                                if ($ride->payment_status == 0) {

//                                    $validator = Validator::make($request->all(), [
//                                        "total_amount" => "required",
//                                    ]);
//                                    if ($validator->fails()) {
//                                        return response()->json([
//                                            "status" => 0,
//                                            "message" => $validator->errors()->first(),
//                                            "message_code" => 9,
//                                        ]);
//                                    }
                                    if($ride->is_hail != 1) {
                                        $ride->status = $request_status;
                                        $ride->payment_status = 1;
                                        $ride->save();

                                        WalletSettlementHelper::settleDriverCommissionOnCashRide(
                                            $ride,
                                            $this->notificationClass,
                                            request()->get('general_settings')
                                        );

                                        if($ride->ride_for_other == 1 && $ride->payment_status != 1) {
                                            $ride->status = 8;
                                            $ride->payment_type = 1;
                                            $ride->save();
                                        }
                                    } else {
                                        $ride->status = 8;
                                        $ride->payment_status = 1;
                                        WalletSettlementHelper::settleDriverCommissionOnCashRide(
                                            $ride,
                                            $this->notificationClass,
                                            request()->get('general_settings')
                                        );
                                        $ride->save();
                                    }
                                    return response()->json([
                                        "status" => 1,
//                                        "message" => "success!",
                                        "message" => __('driver_messages.1'),
                                        "message_code" => 1,
                                        "ride_id" => $ride->id,
                                        "ride_status" => $ride->status - 0,
                                        "ride_completed_status" => 0,
                                        "ride_cancelled_status" => 0,
                                        "driver_current_status" => $driver_details->current_status,
                                        "cancel_by" => $cancel_message
                                    ]);
                                } else {
                                    $ride->status = $request_status;
                                    $ride->save();
                                    return response()->json([
                                        "status" => 1,
//                                        "message" => "success!",
                                        "message" => __('driver_messages.1'),
                                        "message_code" => 1,
                                        "ride_id" => $ride->id,
                                        "ride_status" => $ride->status - 0,
                                        "ride_completed_status" => 0,
                                        "ride_cancelled_status" => 0,
                                        "driver_current_status" => $driver_details->current_status,
                                        "cancel_by" => $cancel_message,
                                        "way_point_status" => $way_point_status - 0,
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } elseif ($request_status == 8) {
                            if ($ride_status == 4 || $ride_status == 10) {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "ride cancelled!",
                                    "message" => __('driver_messages.65'),
                                    "message_code" => 65,
                                ]);
                            } elseif ($ride_status == 6 || $ride_status == 7) {
                                $validator = Validator::make($request->all(), [
                                    "rating" => "numeric",
                                    "comment" => "nullable"
                                ]);
                                if ($validator->fails()) {
                                    return response()->json([
                                        "status" => 0,
                                        "message" => $validator->errors()->first(),
                                        "message_code" => 9,
                                    ]);
                                }
                                //if ($ride->payment_status == 0) {
                                //    return response()->json([
                                //        "status" => 0,
                                //        "message" => "payment process pending!",
                                //        "message_code" => 83,
                                //    ]);
                                //}
                                WalletSettlementHelper::markCashRidePaidIfNeeded(
                                    $ride,
                                    $this->notificationClass,
                                    request()->get('general_settings')
                                );
                                $ride->status = $request_status;
                                $ride->save();
                                if ($request->get('rating') != Null || $request->get('comment') != Null) {
                                    if ($request->get('rating') != Null) {
                                        $user_rating = new UserRatings();
                                        $user_rating->user_id = $ride->user_id;
                                        $user_rating->provider_id = $driver_id;
                                        $user_rating->ride_book_id = $ride->id;
                                        $user_rating->rating = round($request->get('rating'), 2);
                                        $user_rating->comment = $request->get('comment') != Null ? trim($request->get('comment')) : Null;
                                        $user_rating->save();

                                        $user_details = User::query()->where('id', $ride->user_id)->first();
                                        if ($user_details != Null) {
                                            $ratings = UserRatings::query()->select(DB::raw('avg(rating) as ratings'))
                                                ->groupBy('user_id')
                                                ->where('user_id', $user_details->id)
                                                ->first();
                                            $user_details->rating = round($ratings->ratings,2);
                                            $user_details->save();
                                        }
                                    }
                                }
                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $driver_details->current_status,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "something went to wrong!",
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } elseif ($request_status == 9) {
                            if ($ride_status == 4 || $ride_status == 10) {
                                return response()->json([
                                    "status" => 0,
//                                    "message" => "ride cancelled!",
                                    "message" => __('driver_messages.65'),
                                    "message_code" => 65,
                                ]);
                            } elseif ($ride_status == 6 || $ride_status == 7 || $ride_status == 8) {
                                $update_driver_status = User::query()->where('id', $request->get('user_id'))->first();
                                if ($update_driver_status == Null) {
                                    return response()->json([
                                        "status" => 0,
//                                        "message" => "something went to wrong!",
                                        "message" => __('driver_messages.9'),
                                        "message_code" => 9,
                                    ]);
                                }
                                $ride->status = $request_status;
                                $ride->save();

                                $update_driver_status->driver_current_status = 1;
                                $update_driver_status->save();
                                if($ride->is_hail != 1) {
                                    $this->notificationClass->userTransportNotification($ride->id, $user->device_token, $request_status, $user->login_device, $user->language);
                                    ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('user_id', $ride->user_id)->where('booking_id', $ride->id)->delete();
                                } else{
                                    ProviderUserRunningService::query()->where('provider_id', $driver_id)->where('booking_id', $ride->id)->delete();
                                }

                                //deleting chat from firebase
                                if((new FirebaseService())->deleteOrderChat($ride->ride_no,$ride->id)){
                                }

                                if($ride->is_hail != 1) {
                                    $general_settings = request()->get("general_settings");
                                    if ($general_settings != Null) {
                                        if ($general_settings->send_mail == 1) {
                                            //sending mail user
                                            $email = $user->email;
                                            $ride_id = "#" . $ride->ride_no;
                                            $date_time = date("Y-m-d h:i:s", strtotime($ride->updated_at));
                                            try {
                                                $mail_type = "request_completed";
                                                $to_mail = $email;
                                                $subject = "Your " . $general_settings->mail_site_name . " Ride Request Completed";
                                                $disp_data = array("##user_name##" => $ride->user_name, "##ride_id##" => $ride_id);
                                                $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                            } catch (\Exception $e) {
                                            }

                                            //sending mail driver
                                            try {
                                                $email = $driver_check['email'];
                                                $driver_name = ucwords($driver_check['first_name']);
                                                $mail_type = "driver_ride_completed_-_transport";
                                                $to_mail = $email;
                                                $subject = $general_settings->mail_site_name . " Ride Completed";
                                                $disp_data = array("##driver_name##" => $driver_name, "##ride_id##" => $ride_id, "##date_time##" => $date_time);
                                                $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                                            } catch (\Exception $e) {
                                            }
                                        }
                                    }
                                }
                                return response()->json([
                                    "status" => 1,
//                                    "message" => "success!",
                                    "message" => __('driver_messages.1'),
                                    "message_code" => 1,
                                    "ride_id" => $ride->id,
                                    "ride_status" => $request_status - 0,
                                    "ride_completed_status" => 0,
                                    "ride_cancelled_status" => 0,
                                    "driver_current_status" => $update_driver_status->driver_current_status - 0,
                                    "cancel_by" => $cancel_message,
                                    "way_point_status" => $way_point_status - 0,
                                ]);
                            } else {
                                return response()->json([
                                    "status" => 0,
                                    "message" => __('driver_messages.9'),
                                    "message_code" => 9,
                                ]);
                            }
                        } else {
                            return response()->json([
                                "status" => 0,
                                "message" => __('driver_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.84'),
                        "message_code" => 84,
                    ]);
                }
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.26'),
                    "message_code" => 26,
                ]);
            }
    }

    public function postDriverRideDetails(Request $request)
    {
        $this->notificationClass->ApiLogDetail($logger_type = 2, $request->get('driver_id'), "postDriverRideDetails", $request->all());

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
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

        $settings = request()->get('general_settings');

        //code for dynamic Toll charge module 0 - off , 1 - driver will give the final charge , 2 - driver will give no of tolls & charge per toll is decided by admin
        $is_toll_charge = ($settings) ? $settings->is_toll_module : 0;
        $driver_currency = \App\Support\UserCurrencyResolver::forCurrency($driver_check->currency);
        if ($driver_currency == Null) {
            $driver_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $driver_currency->ratio;
        $user_profile_url = url('/assets/images/profile-images/customer');

        if(isset($user_check->language) && $user_check->language != Null  && $user_check->language != "en"){
            $user_lang = $user_check->language."_";
        }else{
            $user_lang = "";
        }
        $driver_language = (isset($user_check->language) && $user_check->language != null && $user_check->language != '')
            ? $user_check->language
            : 'en';

        $rideSelect = [
                'user_ride_booking.id as ride_id',
                'user_ride_booking.ride_no as booking_no',
                'user_ride_booking.vehicle_service_id',
                'user_ride_booking.ride_type as ride_type',
                DB::raw("(CASE WHEN users.avatar != '' THEN (CASE WHEN CHAR_LENGTH(users.avatar) >= 25 THEN users.avatar ELSE concat('$user_profile_url','/',users.avatar) END) ELSE '' END) as user_profile_image"),
                DB::raw("(concat(users.first_name,'')) as user_name"),
                DB::raw("(concat(users.country_code,users.contact_number)) as user_contact_number"),
                'users.id as user_id',
                'users.rating as user_rating',
                'users.device_token as user_fcm_token',
                'user_ride_booking.pickup_address as pickup_address',
                'user_ride_booking.pickup_lat',
                'user_ride_booking.pickup_long',
                'user_ride_booking.destination_latlong',
                'user_ride_booking.additional_request',
                'user_ride_booking.destination_address as destination_address',
                'user_ride_booking.pickup_datetime as pickup_datetime',
                'user_ride_booking.created_at',
                'user_ride_booking.total_pay as total_amount',
                'user_ride_booking.payment_type as payment_type',
                'user_ride_booking.driver_id',
                'user_ride_booking.promo_code',
                'user_ride_booking.refer_discount',
                'user_ride_booking.otp',
                'user_ride_booking.cancel_by',
                'user_ride_booking.cancel_reason',
                'user_ride_booking.status as ride_status',
                'user_ride_booking.ride_for_other',
                'user_ride_booking.other_user_name',
                'user_ride_booking.other_user_contact_number',
                'user_ride_booking.toll_charge',
                'user_ride_booking.offered_price',
                'user_ride_booking.is_way_point',
                'user_ride_booking.way_point_status',
        ];
        if (Schema::hasColumn('user_ride_booking', 'delivery_variant')) {
            $rideSelect[] = 'user_ride_booking.delivery_variant';
        }
        if (Schema::hasColumn('user_ride_booking', 'destination_payment_method')) {
            $rideSelect[] = 'user_ride_booking.destination_payment_method';
        }
        $ride_details = TransportRideBook::query()->select($rideSelect)
                ->leftJoin('users', 'users.id', '=', 'user_ride_booking.user_id')
//                ->whereNull('users.deleted_at')
                ->where('user_ride_booking.id', $request->get('ride_id'))->first();

        if ($ride_details == Null) {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }

        $ridePassenger = User::query()
            ->select('id', 'first_name', 'emergency_contact', 'emergency_country_code', 'country_code')
            ->where('id', '=', $ride_details->user_id)
            ->first();
        $sos = \App\Support\SosContactListHelper::forUser($ridePassenger, $user_lang);

            $driver_details = TransportDriverDetails::query()->select('transport_vehicle_type.name as vehicle_type_name','transport_vehicle_type.service_id','transport_driver_details.vehicle_company','transport_driver_details.model_name', 'transport_driver_details.vehicle_image')
                                ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
                                ->where('transport_driver_details.user_id',$ride_details->driver_id)->first();

            if($driver_details == Null){
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.15'),
                    "message_code" => 15,
                ]);
            }

        if($ride_details->vehicle_service_id == 4){
            $courier_details = TransportCourierDetails::query()->where('ride_id',$ride_details->ride_id)->first();
            if($courier_details != Null){
                $recipient_name = $courier_details->recipient_name;
                $recipient_contact_number = $courier_details->recipient_contact_number;
                $item_description = $courier_details->item_description;
                $estimate_price = $courier_details->estimate_price;
            }
        }

            $total_ratings = UserRatings::query()->where('user_id',$ride_details->user_id)->where('status',1)->count();

            if ($ride_details != Null) {
                $destination_coordinates = explode(',', $ride_details->destination_latlong);
                $address_list = array();
                $address_list[] = [
                    "address" => $ride_details->pickup_address,
                    "address_lat" => trim($ride_details->pickup_lat),
                    "address_long" => trim($ride_details->pickup_long)
                ];
                if ($ride_details->is_way_point == 1) {
                    $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride_details->ride_id)->first();
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
                // invoice link
                $invoice_download_link = "";
                $order_details =  TransportRideBook::query()->select('id','driver_id')->where('driver_id',$ride_details->driver_id)
                    ->where('id','=',$ride_details->ride_id)
                    ->whereIn('status',[4,9,10])
                    ->first();
                if($order_details != Null){
                    $invoice_download_link = route('get:ride-invoice-download',[$order_details->id,"driver",$order_details->driver_id]);
                }
                $tripAmount = \App\Helpers\TripAmountHelper::resolveForCurrency($ride_details, (float) $currency);
                $tripBase = \App\Helpers\TripAmountHelper::resolveBase($ride_details);
                $ride_details_arr = [
                    "ride_id" => $ride_details->ride_id,
                    "user_id" => $ride_details->user_id,
                    "driver_id" => $ride_details->driver_id,
                    "ride_type" => isset($ride_details->ride_type) ? $ride_details->ride_type : 0,
                    'additional_remark' => "" . $ride_details->additional_request,
                    "user_fcm_token" => $ride_details->user_fcm_token,
                    "booking_no" => $ride_details->booking_no,
                    "user_profile_image" => $ride_details->user_profile_image,
                    "user_name" => $ride_details->user_name,
                    "contact_number" => $ride_details->user_contact_number,
                    "pickup_datetime" => $ride_details->pickup_datetime,
                    "service_date_time" => date('Y-m-d H:i:s', strtotime($ride_details->created_at)),
                    "total_amount" => $tripAmount,
                    "payment_type" => $ride_details->payment_type,
                    "ride_status" => $ride_details->ride_status,
                    "refer_discount" => round($ride_details->refer_discount * $currency, 2),
                    "otp" => $ride_details->otp,
                    'is_otp' => $settings->ride_otp,
                    'user_rating'=>$ride_details->user_rating,
                    'vehicle_type_name'=>$driver_details->vehicle_type_name,
                    'vehicle_company'=>$driver_details->vehicle_company,
                    'model_name'=>$driver_details->model_name,
                    'total_ratings'=>$total_ratings,
                    'service_id'=>$ride_details->vehicle_service_id,
                    "cancel_by" => $ride_details->cancel_by != Null ? $ride_details->cancel_by : "",
                    'cancel_reason' => $ride_details->cancel_reason,
                    'recipient_name' => isset($recipient_name) ? $recipient_name : '',
                    'recipient_contact_number' => isset($recipient_contact_number) ? $recipient_contact_number : '',
                    'item_description' => isset($item_description) ? $item_description : '',
                    "invoice_download_link" => $invoice_download_link,
                    "address_list" => $address_list,
                    "ride_for_other" => $ride_details->ride_for_other,
                    "other_user_name" => $ride_details->other_user_name,
                    "other_user_contact_number" => $ride_details->other_user_contact_number,
                    "toll_charge" => round($ride_details->toll_charge * $currency, 2),
                    "ride_fare" => $tripAmount,
                    "way_point_status" => $ride_details->way_point_status,
                    "sos_contact_list" => $sos,
                    "is_toll_charge" => $is_toll_charge,
                    "estimate_price" => isset($estimate_price) ? $estimate_price : 0,
                    "vehicle_image" => url('/assets/images/provider-vehicle-image/'.$driver_details->vehicle_image),
                    "order_chat_number" => (new FirebaseService())->CreateOrderNumberForChat($ride_details->booking_no,$ride_details->ride_id) ,//for fire base chat
                    "destination_payment_method" => $ride_details->destination_payment_method ?? '',
                    "destination_payment_label" => DestinationPaymentHelper::label($ride_details->destination_payment_method ?? null, $driver_language),
                    "driver_can_cancel" => AppMobileSettingsHelper::driverCanCancelRide((int) $ride_details->ride_status),
                ];
                if (in_array((int) $ride_details->ride_status, [4, 9, 10], true)) {
                    $ride_details_arr = array_merge(
                        $ride_details_arr,
                        RideInvoiceHelper::breakdownForCurrency(
                            $tripBase,
                            (float) $currency,
                            null,
                            (int) $ride_details->vehicle_service_id,
                            $ride_details->delivery_variant ?? null
                        )
                    );
                }
                return response()->json([
                    "status" => 1,
//                    "message" => "success!",
                    "message" => __('driver_messages.1'),
                    "message_code" => 1,
                    "ride_details" => $ride_details_arr,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
//                    "message" => "ride details not found!",
                    "message" => __('driver_messages.26'),
                    "message_code" => 26,
                ]);
            }
    }

    public function postDriverRideHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "filter_type" => "nullable|in:0,1,2,3,4,5,6",// 6 for running delivery ride [ for multi delivery module ].
            "order_status" => "nullable",
            "timezone" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        $user_currency = \App\Support\UserCurrencyResolver::forCurrency($driver_check->currency);
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency->ratio;
        $driver_details = TransportDriverDetails::query()->where('user_id', $request->get('user_id'))->first();
        if ($driver_details != Null) {

            $vehicle_type_icon_url = url('/assets/images/service-category/transport-service-type/');
            $ride_history = [];

            if ($driver_check->language != "en") {
                $lang_prefix = $driver_check->language."_";
            }else {
                $lang_prefix = "";
            }

            $timezone = $this->notificationClass->getDefaultTimeZone($request->get('timezone'));
            date_default_timezone_set($timezone);

            $filter_type = $request->get('filter_type');
            $date = date('Y-m-d');
            $transport_running_ride = [];
            //making an array from string
            $order_status =[];
            if($request->get('order_status') != ""){
                $order_status =  explode(",",$request->get('order_status'));
            }

            if ($filter_type == 1) {
                //today
                $start_date = $date . " 00:00:01";
                $end_date = $date . " 23:59:59";
            } elseif ($filter_type == 2) {
                //last 7 day
                $start_date = date('Y-m-d', strtotime('-7 days', strtotime($date)));
                $end_date = $date;
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            } elseif ($filter_type == 4) {
                //this year
                $start_date = date("Y-01-01", strtotime($date));
                $end_date = date("Y-m-d", strtotime($date));
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            } elseif ($filter_type == 5) {
                //upcomming rides
                $start_date = date('Y-m-d', strtotime('+1 days', strtotime($date)));
                $start_date = $start_date . " 00:00:01";
            } elseif ($filter_type == 6) {
                //running rides
                $transport_running_ride = ProviderUserRunningService::query()->where("provider_id", "=", $driver_id)->get()->pluck("booking_id");
            } else {
                //last 30 day
                $start_date = date('Y-m-d', strtotime('-30 days', strtotime($date)));
                $end_date = $date;
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            }

            $per_page = 10;
            if ($request->get('per_page') != Null) {
                $per_page = $request->get('per_page');
            }
            $page = $request->get('page') != Null ? $request->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            $user_profile_url = url('/assets/images/profile-images/customer');
            // for filter type all fire query
            $temp_transport_rides = TransportRideBook::query()->select('user_ride_booking.id as ride_id',
               'user_ride_booking.ride_no',
               'user_ride_booking.pickup_address',
               'user_ride_booking.destination_address',
                'user_ride_booking.status as ride_status','user_ride_booking.created_at','user_ride_booking.pickup_datetime as schedule_service_date_time',
                'user_ride_booking.pickup_datetime as ride_date_time','user_ride_booking.driver_pay_settle_status',
                'user_ride_booking.other_user_name',
                'user_ride_booking.other_user_contact_number',
                'user_ride_booking.is_hail',
                DB::raw('ROUND(user_ride_booking.driver_amount * ' . $currency . ',2) As ride_amount'),
                'user_ride_booking.ride_type',
                (DB::raw('DATE_FORMAT(user_ride_booking.pickup_datetime, "%a %d %b,%Y") as ride_date')),
                (DB::raw('DATE_FORMAT(user_ride_booking.pickup_datetime, "%H:%i") as ride_time')),
                'transport_vehicle_type.name as vehicle_type_name',
                'transport_vehicle_type.service_id',
                'users.first_name as user_name',
                'vehicle_services.'.$lang_prefix.'name as service_name',
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$user_profile_url','/',users.avatar,'?v=0.4')) ELSE '' END) as user_profile"),
                DB::raw("(CASE WHEN transport_vehicle_type.icon_name!='' THEN concat('$vehicle_type_icon_url','/',transport_vehicle_type.icon_name) ELSE '' END) as vehicle_type_icon_name"))
                ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id')
                ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
                ->leftjoin('users', 'users.id', '=', 'user_ride_booking.user_id')
                ->where('user_ride_booking.driver_id', $driver_details->user_id);

            if ($filter_type == 1 || $filter_type == 2 || $filter_type == 3 || $filter_type == 4) {
                $transport_rides = $temp_transport_rides->where('user_ride_booking.pickup_datetime', '>=', $start_date)
                    ->where('user_ride_booking.pickup_datetime', '<=', $end_date);

            } elseif ($filter_type == 5) {
                $transport_rides = $temp_transport_rides->where('user_ride_booking.pickup_datetime', '>', $start_date)
                    ->where('user_ride_booking.status','!=','4');
            } elseif ($filter_type == 6) {
                $transport_rides = $temp_transport_rides->whereIn('user_ride_booking.id', $transport_running_ride);
            } else {
                // take default
                $transport_rides = $temp_transport_rides;
            }

            //applying order status filter
            //order_status= 1-on-going, 2-completed, 3-cancelled, 4-pending
            if($request->get('order_status') != null){
                $ride_status_array=[];

                if(in_array(1,$order_status)){
                    //on-going rides filter
                    $ride_status_array=array_merge($ride_status_array,[ 3, 5, 6, 7, 8]);
                }
                if (in_array(2,$order_status)){
                    //completed rides filter
                    array_push($ride_status_array,9);
                }
                if (in_array(3,$order_status)){
                    //cancelled rides filter
                    array_push($ride_status_array,4);
                }
                if (in_array(4,$order_status)){
                    //pending rides filter
                    array_push($ride_status_array,0);
                }
                $transport_rides = $transport_rides->whereIn('user_ride_booking.status', $ride_status_array);
            }

            $total_count = $transport_rides->count();
            $transport_rides = $transport_rides->get()->toArray();


            $ride_history = array_merge($ride_history, $transport_rides);
            //$ride_history = array_slice($ride_history,$offset,$per_page);

            if ($ride_history != Null) {
                usort($ride_history, function ($a, $b) {
                    if ($a['ride_date_time'] == $b['ride_date_time']) return 0;
                    return $a['ride_date_time'] < $b['ride_date_time'] ? 1 : -1;
                });
            }
            $complete_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 9);
            });
            $pending_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 1);
            });
            $cancelled_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 4 || $var['ride_status'] == 10);
            });
            $total_revenues = round(array_sum(array_column((array_filter($complete_ride, function ($var) {
                if($var['ride_status'] == 9 && $var['driver_pay_settle_status'] == 0){
                    return  $var['ride_amount'];
                }
            })
            ),'ride_amount')),2);

            $ride_history_count = count($ride_history);

            $ride_history = array_slice($ride_history,$offset,$per_page);

            $ride_history_new = [];
            foreach ($ride_history as $key=> $singel){
                $ride_history_new[$key]['ride_id'] = $singel['ride_id'];
                $ride_history_new[$key]['ride_no'] = $singel['ride_no'];
                $ride_history_new[$key]['user_name'] = $singel['user_name'];
                $ride_history_new[$key]['user_profile'] = $singel['user_profile'];
                $ride_history_new[$key]['driver_pay_settle_status'] = $singel['driver_pay_settle_status'];
                $ride_history_new[$key]['vehicle_type_icon'] = $singel['vehicle_type_icon_name'];
                $ride_history_new[$key]['service_id'] = $singel['service_id'];
                $ride_history_new[$key]['pickup_address'] = $singel['pickup_address'];
                $ride_history_new[$key]['destination_address'] = $singel['destination_address'];
                $ride_history_new[$key]['ride_status'] = $singel['ride_status'];
                $ride_history_new[$key]['ride_date_time'] = $singel['ride_date_time'];
                $ride_history_new[$key]['ride_amount'] = $singel['ride_amount'];
                $ride_history_new[$key]['ride_type'] = $singel['ride_type'];
                $ride_history_new[$key]['ride_date'] = $this->notificationClass->dateLangConvert($singel['ride_date'],$driver_check->language);
                $ride_history_new[$key]['ride_time'] = $singel['ride_time'];
                $ride_history_new[$key]['service_date_time'] = date('Y-m-d H:i:s',strtotime($singel['created_at']));
                $ride_history_new[$key]['schedule_service_date_time'] = $singel['schedule_service_date_time'];
                $ride_history_new[$key]['service_name'] = $singel['service_name'];
                $ride_history_new[$key]['other_user_name'] = $singel['other_user_name'];
                $ride_history_new[$key]['other_user_contact_number'] = $singel['other_user_contact_number'];
                $ride_history_new[$key]['is_hail'] = $singel['is_hail'];

            }

            $current_page = $page;
            $divider = round($total_count / $per_page);
            if($divider > ($total_count / $per_page)){
                $last_page = $divider;
            } else{
                $last_page = $divider + 1;
            }

            return response()->json([
                "status" => 1,
//                "message" => "success!",
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                'current_page' => $current_page - 0,
                'last_page' => $last_page - 0,
                'total' => $total_count - 0,
                "total_revenues" => round($total_revenues * $currency, 2),
                "completed_ride" => count($complete_ride),
//                "total_ride" => count($pending_ride),
                "total_ride" => $ride_history_count,
                "cancelled_ride" => count($cancelled_ride),
                "ride_history" => $ride_history_new
            ]);
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "driver details not found",
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }
    }

    public function postDriverEarning(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "filter_type" => "nullable|in:0,1,2,3,4,5,6",// 6 for running delivery ride [ for multi delivery module ]
            "timezone" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        $user_currency = \App\Support\UserCurrencyResolver::forCurrency($driver_check->currency);
        if ($user_currency == Null) {
            $user_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $user_currency->ratio;
        $driver_details = TransportDriverDetails::query()->where('user_id', $request->get('user_id'))->first();
        if ($driver_details != Null) {

            $vehicle_type_icon_url = url('/assets/images/service-category/transport-service-type/');
            $ride_history = [];

            if ($driver_check->language != "en") {
                $lang_prefix = $driver_check->language."_";
            }else {
                $lang_prefix = "";
            }
            $timezone = $this->notificationClass->getDefaultTimeZone($request->get('timezone'));
            date_default_timezone_set($timezone);

            $filter_type = $request->get('filter_type');
            $date = date('Y-m-d');
            $transport_running_ride = [];

            if ($filter_type == 1) {
                //today
                $start_date = $date . " 00:00:01";
                $end_date = $date . " 23:59:59";
            } elseif ($filter_type == 2) {
                //last 7 day
                $start_date = date('Y-m-d', strtotime('-7 days', strtotime($date)));
                $end_date = $date;
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            } elseif ($filter_type == 4) {
                //this year
                $start_date = date("Y-01-01", strtotime($date));
                $end_date = date("Y-m-d", strtotime($date));
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            } elseif ($filter_type == 5) {
                //upcomming rides
                $start_date = date('Y-m-d', strtotime('+1 days', strtotime($date)));
                $start_date = $start_date . " 00:00:01";
            } elseif ($filter_type == 6) {
                //running rides
                $transport_running_ride = ProviderUserRunningService::query()->where("provider_id", "=", $driver_id)->get()->pluck("booking_id");
            } else {
                //last 30 day
                $start_date = date('Y-m-d', strtotime('-30 days', strtotime($date)));
                $end_date = $date;
                $start_date = $start_date . " 00:00:01";
                $end_date = $end_date . " 23:59:59";
            }

            $per_page = 10;
            if ($request->get('per_page') != Null) {
                $per_page = $request->get('per_page');
            }
            $page = $request->get('page') != Null ? $request->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            $user_profile_url = url('/assets/images/profile-images/customer');
            // for filter type all fire query
            $temp_transport_rides = TransportRideBook::query()->select('user_ride_booking.id as ride_id',
                'user_ride_booking.ride_no as order_no',
                'user_ride_booking.pickup_address',
                'user_ride_booking.destination_address',
                'user_ride_booking.status as ride_status','user_ride_booking.created_at','user_ride_booking.pickup_datetime as schedule_service_date_time',
                'user_ride_booking.pickup_datetime as ride_date_time','user_ride_booking.driver_pay_settle_status',
                DB::raw('ROUND(user_ride_booking.driver_amount * ' . $currency . ',2) As ride_amount'),
                'user_ride_booking.ride_type',
                (DB::raw('DATE_FORMAT(user_ride_booking.pickup_datetime, "%a %d %b,%Y") as ride_date')),
                (DB::raw('DATE_FORMAT(user_ride_booking.pickup_datetime, "%H:%i") as ride_time')),
                'transport_vehicle_type.name as vehicle_type_name',
                'transport_vehicle_type.service_id',
                'users.first_name as user_name',
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$user_profile_url','/',users.avatar,'?v=0.4')) ELSE '' END) as user_profile"),
                DB::raw("(CASE WHEN transport_vehicle_type.icon_name!='' THEN concat('$vehicle_type_icon_url','/',transport_vehicle_type.icon_name) ELSE '' END) as vehicle_type_icon_name"))
                ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id')
                ->join('users', 'users.id', '=', 'user_ride_booking.user_id')
                ->where('user_ride_booking.driver_id', $driver_details->user_id)
                ->where('user_ride_booking.status',9);

            if ($filter_type == 1 || $filter_type == 2 || $filter_type == 3 || $filter_type == 4) {
                $transport_rides = $temp_transport_rides->where('user_ride_booking.pickup_datetime', '>=', $start_date)
                    ->where('user_ride_booking.pickup_datetime', '<=', $end_date);

            } elseif ($filter_type == 5) {
                $transport_rides = $temp_transport_rides->where('user_ride_booking.pickup_datetime', '>', $start_date)
                    ->where('user_ride_booking.status','!=','4');
            } elseif ($filter_type == 6) {
                $transport_rides = $temp_transport_rides->whereIn('user_ride_booking.id', $transport_running_ride);
            } else {
                // take default
                $transport_rides = $temp_transport_rides;
            }

            $total_count = $transport_rides->count();
            $transport_rides = $transport_rides->get()->toArray();


            $ride_history = array_merge($ride_history, $transport_rides);
            //$ride_history = array_slice($ride_history,$offset,$per_page);

            if ($ride_history != Null) {
                usort($ride_history, function ($a, $b) {
                    if ($a['ride_date_time'] == $b['ride_date_time']) return 0;
                    return $a['ride_date_time'] < $b['ride_date_time'] ? 1 : -1;
                });
            }
            $complete_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 9);
            });
            $pending_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 1);
            });
            $cancelled_ride = array_filter($ride_history, function ($var) {
                return ($var['ride_status'] == 4 || $var['ride_status'] == 10);
            });
            $total_revenues = round(array_sum(array_column((array_filter($complete_ride, function ($var) {
                if($var['ride_status'] == 9 && $var['driver_pay_settle_status'] == 0){
                    return  $var['ride_amount'];
                }
            })
            ),'ride_amount')),2);

            $ride_history_count = count($ride_history);

            $ride_history = array_slice($ride_history,$offset,$per_page);

            $ride_history_new = [];
            foreach ($ride_history as $key=> $singel){
                $ride_history_new[$key]['ride_id'] = $singel['ride_id'];
                $ride_history_new[$key]['user_name'] = $singel['user_name'];
                $ride_history_new[$key]['user_profile'] = $singel['user_profile'];
                $ride_history_new[$key]['driver_pay_settle_status'] = $singel['driver_pay_settle_status'];
                $ride_history_new[$key]['vehicle_type_icon'] = $singel['vehicle_type_icon_name'];
                $ride_history_new[$key]['service_id'] = $singel['service_id'];
                $ride_history_new[$key]['pickup_address'] = $singel['pickup_address'];
                $ride_history_new[$key]['destination_address'] = $singel['destination_address'];
                $ride_history_new[$key]['ride_status'] = $singel['ride_status'];
                $ride_history_new[$key]['ride_date_time'] = $singel['ride_date_time'];
                $ride_history_new[$key]['ride_amount'] = $singel['ride_amount'];
                $ride_history_new[$key]['ride_type'] = $singel['ride_type'];
                $ride_history_new[$key]['ride_date'] = $this->notificationClass->dateLangConvert($singel['ride_date'],$driver_check->language);
                $ride_history_new[$key]['ride_time'] = $singel['ride_time'];
                $ride_history_new[$key]['service_date_time'] = date('Y-m-d H:i:s',strtotime($singel['created_at']));
                $ride_history_new[$key]['schedule_service_date_time'] = $singel['schedule_service_date_time'];

            }

            $current_page = $page;
            $divider = round($total_count / $per_page);
            if($divider > ($total_count / $per_page)){
                $last_page = $divider;
            } else{
                $last_page = $divider + 1;
            }

            return response()->json([
                "status" => 1,
//                "message" => "success!",
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                'current_page' => $current_page - 0,
                'last_page' => $last_page - 0,
                'total' => $total_count - 0,
                "total_revenues" => round($total_revenues * $currency, 2),
                "completed_ride" => count($complete_ride),
//                "total_ride" => count($pending_ride),
                "total_ride" => $ride_history_count,
                "cancelled_ride" => count($cancelled_ride),
                "ride_history" => $ride_history_new
            ]);
        } else {
            return response()->json([
                "status" => 0,
//                "message" => "driver details not found",
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }
    }

    public function postGetDriverFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');

        $user_check = $this->userClassapi->checkUserAllow($request->get('user_id'), $request->get('access_token'));
        if ($failed = $this->userClassapi->authJsonResponse($user_check)) {
            return $failed;
        }

        $driver_check = $this->userClassapi->checkDriverRegisterAllow($request->get('user_id'));
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        $driver_details = TransportDriverDetails::query()->where('user_id', $driver_id)->first();
        if ($driver_details != Null) {
            $feedback_list = [];
            if ($driver_check->language != "en") {
                $lang_prefix = $driver_check->language."_";
            } else {
                $lang_prefix = "";
            }

            $user_profile_url = url('/assets/images/profile-images/customer');
            $ride_ratings = TransportRatings::query()->select('transport_driver_rating.id as rating_id',
                'transport_driver_rating.ride_id as ride_id', DB::raw("concat(users.first_name,'')as user_name"),
                DB::raw("(CASE WHEN users.avatar != '' THEN (CASE WHEN CHAR_LENGTH(users.avatar) >= 25 THEN users.avatar ELSE concat('$user_profile_url','/',users.avatar) END) ELSE '' END) as user_profile_image"),
                'transport_driver_rating.rating', DB::raw("(CASE WHEN transport_driver_rating.comment != '' THEN transport_driver_rating.comment ELSE '' END) as comment"),
                (DB::raw('transport_driver_rating.created_at as datetime')),
            )
                ->join('users', 'users.id', '=', 'transport_driver_rating.user_id')
                ->join('user_ride_booking', 'user_ride_booking.id', '=', 'transport_driver_rating.ride_id')
                ->where('transport_driver_rating.status', 1)
                ->where('transport_driver_rating.driver_id', $driver_id)
                ->whereNull('users.deleted_at')
                ->whereNotNull('transport_driver_rating.ride_id')
                ->get()
                ->toArray();

            $feedback_list = array_merge($feedback_list, $ride_ratings);
            if ($feedback_list != Null) {
                usort($feedback_list, function ($a, $b) {
                    if ($a['datetime'] == $b['datetime']) return 0;
                    return $a['datetime'] < $b['datetime'] ? 1 : -1;
                });
            }
            $feedback_data = [];
            foreach ($feedback_list as $feedback){
                $feedback_data[] = [
                    'rating_id' => $feedback['rating_id'],
                    'ride_id' => $feedback['ride_id'],
                    'user_name' => $feedback['user_name'],
                    'user_profile_image' => $feedback['user_profile_image'],
                    'rating' => $feedback['rating'],
                    'comment' => $feedback['comment'],
                    'datetime' => $this->notificationClass->dateLangConvert(Date('D d M, Y', strtotime($feedback['datetime'])),$driver_check->language),
                ];
            }
            return response()->json([
                "status" => 1,
                "message" => __('driver_messages.1'),
                "message_code" => 1,
                "feedback_list" => $feedback_data
            ]);
        }
        else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.15'),
                "message_code" => 15,
            ]);
        }
    }

    public function postDriverGetRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required"
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

        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_check);

        $language = $user_check->language;
        if ($language != "en" && $language != "" && $language != "Null") {
            $lang_prefix = $language . "_";
        } else {
            $lang_prefix = "";
        }

        $service_setting = ServiceSettings::query()->first();
        if ($service_setting != Null) {
            $radius = $service_setting->provider_search_radius;
            $timeout = $service_setting->provider_accept_timeout;
        }
        else {
            $radius = 5;
            $timeout = 60;
        }

        $driver_details = TransportDriverDetails::query()
            ->select('transport_driver_details.*','transport_vehicle_type.service_id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
            ->where('user_id',$request->get('user_id'))->first();
        if($driver_details == Null){
            return response()->json([
                'status' => 5,
                'message' => __('driver_messages.5'),
                'message_code' => 5,
            ]);
        }

        if (! \App\Helpers\RideDriverEligibilityHelper::driverCanServePendingRide($driver_details, (int) $request->get('ride_id'))) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.343',[],$language),
                'message_code' => 343,
            ]);
        }

        $avatar = url('/assets/images/profile-images/customer/');

        $current_lat = $driver_details->current_lat;
        $current_long = $driver_details->current_long;

        $available_ride = TransportRideBook::query()
            ->select('user_ride_booking.id as ride_id','user_ride_booking.ride_no','user_ride_booking.pickup_address','user_ride_booking.destination_address','users.rating','user_ride_booking.pickup_datetime',
                'user_ride_booking.user_name','user_ride_booking.pickup_lat','user_ride_booking.pickup_long','user_ride_booking.user_id','user_ride_booking.vehicle_service_id as service_id','user_ride_booking.payment_type','user_ride_booking.ride_type','user_ride_booking.is_auto_accept','user_ride_booking.destination_payment_method',
                'user_courier_service_details.recipient_name','user_courier_service_details.recipient_contact_number','user_courier_service_details.item_description','user_courier_service_details.estimate_price','user_courier_service_details.package_weight_kg','user_courier_service_details.package_height_cm','user_courier_service_details.package_width_cm','user_courier_service_details.package_length_cm',
                DB::raw('COALESCE(NULLIF(user_ride_booking.offered_price, 0), user_ride_booking.total_pay) * '.$currency.' as offered_price'),
                DB::raw("COUNT(user_rating.id) as total_ratings "),'user_ride_booking.additional_request as additional_remarks',
//                                            DB::raw("TIMESTAMPDIFF(SECOND, user_ride_booking.created_at, NOW()) AS order_time"),
                DB::raw("SUBSTRING_INDEX(user_ride_booking.destination_latlong,',',1) as destination_lat"),
                DB::raw("SUBSTRING_INDEX(user_ride_booking.destination_latlong,',',-1) as destination_long"),
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$avatar','/',users.avatar,'?v=0.4')) ELSE '' END) as profile_image"),
                DB::raw("
                                                CASE WHEN (MINUTE(TIMEDIFF(user_ride_booking.created_at, NOW())) >= 1)
                                                THEN
                                                    CONCAT(MINUTE(TIMEDIFF(user_ride_booking.created_at, NOW())) ,'min ',SECOND(TIMEDIFF(user_ride_booking.created_at, NOW())),'sec ago')
                                                ELSE
                                                    CONCAT(TIMESTAMPDIFF(SECOND, user_ride_booking.created_at, NOW()),'sec ago')
                                                END
                                                AS order_time"),
                DB::raw("ROUND((6371 * acos( cos( radians(pickup_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(pickup_long) ) + sin( radians(pickup_lat) ) * sin(radians( " .$current_lat. " ) ) ) ), 2) as distance" ),
                DB::raw("ROUND((((6371 * acos( cos( radians(pickup_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(pickup_long) ) + sin( radians(pickup_lat) ) * sin(radians( " .$current_lat. " ) ) ) ) / 40 ) * 60 ), 2) as time" )
            )
            ->join('users','users.id','=','user_ride_booking.user_id')
            ->leftjoin('user_courier_service_details','user_courier_service_details.ride_id','=','user_ride_booking.id')
            ->leftJoin('user_rating','user_rating.user_id','=','users.id')
            ->where('user_ride_booking.id',$request->get('ride_id'))
            ->where('user_ride_booking.status',0)
            ->where(function ($assignedDriverScope) use ($request) {
                $driverUserId = (int) $request->get('user_id');
                $assignedDriverScope->whereNull('user_ride_booking.driver_id')
                    ->orWhere('user_ride_booking.driver_id', 0)
                    ->orWhere('user_ride_booking.driver_id', $driverUserId);
            })
            ->where('users.status',1)
            ->whereNull('users.deleted_at')
            ->when($driver_details->child_seat != 1, function ($q) {
                $q->where('user_ride_booking.child_seat', 0);
            })
            ->when($driver_details->handicap != 1, function ($q) {
                $q->where('user_ride_booking.handicap', 0);
            })
            ->first();

        if($available_ride == NULL || $available_ride->ride_id == NULL){
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.343',[],$language),
                'message_code' => 343,
            ]);
        }

        $service = VehicleService::query()->select('id as service_id',$lang_prefix.'name as service_name','max_bargain_percent as min_offer_fare_amount',
            DB::raw('cost_for_km * ' . $currency . ' as cost_for_km'))
            ->where('id',$available_ride->service_id)
            ->where('status',1)
            ->first();

        if ($service == Null) {
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }

        $ride_way_point = UserRideWayPoint::query()->where('ride_id', $available_ride->ride_id)->first();
        $address_list = array();
        $address_list[] = [
            "address" => $available_ride->pickup_address,
            "address_lat" => trim($available_ride->pickup_lat),
            "address_long" => trim($available_ride->pickup_long)
        ];
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
        $address_list[] = [
            "address" => $available_ride->destination_address,
            "address_lat" => trim($available_ride->destination_lat),
            "address_long" => trim($available_ride->destination_long)
        ];

        $general_settings = request()->get("general_settings");
        if ($general_settings == Null) {
            return response()->json([
                "status" => 0,
                "message" => "something went to wrong!",
                "message_code" => 9,
            ]);
        }

        return response()->json([
            "status" => 1,
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "ride_id" => $available_ride->ride_id,
            "ride_no" => $available_ride->ride_no,
            "rating" => $available_ride->rating,
            "offered_price" => $available_ride->offered_price,
            "user_name" => $available_ride->user_name,
            "profile_image" => $available_ride->profile_image,
            "order_time" => $available_ride->order_time,
            "distance" => $available_ride->distance,
            "time" => $available_ride->time,
            "total_ratings" => $available_ride->total_ratings,
            "additional_remarks" => $available_ride->additional_remarks,
            "service_id" => $available_ride->service_id,
            "recipient_name" => isset($available_ride->recipient_name) ? $available_ride->recipient_name : '',
            "recipient_contact_number" => isset($available_ride->recipient_contact_number) ? $available_ride->recipient_contact_number : '',
            "item_description" => isset($available_ride->item_description) ? $available_ride->item_description : '',
            "payment_type" => $available_ride->payment_type,
            "driver_service" => $service,
            "service_name" => $service->service_name,
            "address_list" => $address_list,
            "driver_price_suggestion" => $general_settings->driver_price_suggestion != Null ? $general_settings->driver_price_suggestion : 1,
            "ride_type" => $available_ride->ride_type,
            "is_auto_accept" => $available_ride->is_auto_accept,
            "schedule_date" => $available_ride->pickup_datetime,
            "destination_payment_method" => $available_ride->destination_payment_method ?? '',
            "destination_payment_label" => DestinationPaymentHelper::label($available_ride->destination_payment_method ?? null, $language ?? 'en'),
            "estimate_price" => $available_ride->estimate_price ?? 0,
            "package_weight_kg" => $available_ride->package_weight_kg ?? null,
            "package_height_cm" => $available_ride->package_height_cm ?? null,
            "package_width_cm" => $available_ride->package_width_cm ?? null,
            "package_length_cm" => $available_ride->package_length_cm ?? null,
            "is_delivery" => RideKindHelper::isDeliveryFlag($available_ride),
        ]);

    }

    public function postGetDriverList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "current_lat" => "required",
            "current_long" => "required"
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

        $service_setting = ServiceSettings::query()->first();
        if ($service_setting != Null) {
            $radius = $service_setting->provider_search_radius;
        }
        else {
            $radius = 5;
        }

        $current_lat = $request->get('current_lat');
        $current_long = $request->get('current_long');
        $avatar = url('/assets/images/profile-images/customer/');

        $not_multi_delivery_provider_id = ProviderUserRunningService::query()->get()->pluck('provider_id');

        $get_drivers = User::query()
            ->select(
                'users.first_name','transport_driver_details.vehicle_company','transport_driver_details.plat_no','transport_driver_details.model_year','transport_driver_details.model_name','transport_driver_details.vehicle_color',
                'transport_driver_details.current_lat','transport_driver_details.current_long',
                'transport_vehicle_type.service_id',
                DB::raw("(CASE WHEN users.avatar != '' THEN (concat('$avatar','/',users.avatar,'?v=0.4')) ELSE '' END) as driver_profile"),
                DB::raw("ROUND((6371 * acos( cos( radians(current_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(current_long) ) + sin( radians(current_lat) ) * sin(radians( " .$current_lat. " ) ) ) ), 2) as distance" )
            )
            ->join('transport_driver_details','transport_driver_details.user_id','=','users.id')
            ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
            ->whereRaw(DB::raw("(6371 * acos( cos( radians(current_lat) ) * cos( radians(" .$current_lat. ") )  * cos( radians( " .$current_long. " ) - radians(current_long) ) + sin( radians(current_lat) ) * sin(radians( " .$current_lat. " ) ) ) ) < " . $radius))
            ->where('users.driver_current_status',1)
            ->where('users.is_driver_type',1)
            ->where('users.is_driver_status',1)
            ->where('users.status',1)
            ->whereNotIn('users.id', $not_multi_delivery_provider_id)
            ->whereNull('users.deleted_at')
            ->get()
            ->toArray();

        return response()->json([
            "status" => 1,
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "driver_list" => $get_drivers
        ]);
    }

    //driver bank details api
    public function postGetDriverBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($driver_id);
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }
        $bank_details = ProviderBankDetails::query()->where('provider_id', $driver_id)->first();
        if ($bank_details == Null) {
            return response()->json([
                "status" => 0,
                //"message" => "driver bank details not found!",
                "message" => __('driver_messages.79'),
                "message_code" => 79,
            ]);
        }
        return response()->json([
            "status" => 1,
            //"message" => "success!",
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "account_number" => $bank_details->account_number,
            "holder_name" => $bank_details->holder_name,
            "bank_name" => $bank_details->bank_name,
            "bank_location" => $bank_details->bank_location,
            "payment_email" => $bank_details->payment_email,
            "bic_swift_code" => $bank_details->bic_swift_code,
        ]);
    }

    //driver bank update details api
    public function postUpdateDriverBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "account_number" => "required|numeric",
            "holder_name" => "required",
            "bank_name" => "required",
            "bank_location" => "required",
            "payment_email" => "required|email",
            "bic_swift_code" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($driver_id);
        $bank_details = ProviderBankDetails::query()->where('provider_id', $driver_id)->first();
        if ($bank_details == Null) {
            $bank_details = new ProviderBankDetails();
        }
        $bank_details->provider_id = $driver_id;
        $bank_details->account_number = $request->get('account_number');
        $bank_details->holder_name = $request->get('holder_name');
        $bank_details->bank_name = $request->get('bank_name');
        $bank_details->bank_location = $request->get('bank_location');
        $bank_details->payment_email = $request->get('payment_email');
        $bank_details->bic_swift_code = $request->get('bic_swift_code');
        $bank_details->save();

        return response()->json([
            "status" => 1,
            //"message" => "success!",
            "message" => __('driver_messages.1'),
            "message_code" => 1,
        ]);
    }

    public function postDriverHome(Request $request)
    {
        $this->notificationClass->ApiLogDetail(0, 0, "postDriverHome", $request->all());

        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "app_version" => "required",
            "search_distance" => "nullable",
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

        $search_radius = SearchRadius::query()->select('id','radius')->orderBy('radius')->get();

        $transport_details = TransportDriverDetails::query()->where('user_id',$request->get('user_id'))->first();
        if($transport_details == NULL){
            return response()->json([
                'status' => 5,
                'message' => __('driver_messages.15'),
                'message_code' => 5,
            ]);
        }

        $user_check->app_version = $request->get("app_version");

        $current_lat = $request->get("current_lat");
        $current_long = $request->get("current_long");
        $area_id = 0;
        if ($current_lat != Null && $current_long != Null) {
            $get_admin_area_list = AdminAreaList::query()->where('status', 1)->get();
            $this->notificationClass->ApiLogDetail(2, 0, "get_admin_area_list", $get_admin_area_list);
            if ($get_admin_area_list->isNotEmpty()) {
                foreach ($get_admin_area_list as $get_area) {
                    $this->notificationClass->ApiLogDetail($logger_type = 2, 0, "get_area", $get_area);
                    $vertices_x = explode(",", $get_area->latitude);    // x-coordinates of the vertices of the polygon
                    $vertices_y = explode(",", $get_area->longitude); // y-coordinates of the

                    $points_polygon = count($vertices_x) - 1;  // number vertices - zero-based array
                    $longitude_x = $current_lat;  // x-coordinate of the point to test
                    $latitude_y = $current_long;   // y-coordinate of the point to test

                    if ($this->adminClass->is_in_restricted_area($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)) {
                        $area_id = $get_area->id;
                        break;
                    }
                }
                $user_check->area_id = $area_id;
                $this->notificationClass->ApiLogDetail(2, 0, "area_id", $area_id);
            }
        }
        $user_check->save();

        $transport_details->search_distance_filter = $request->get('search_distance') != NULL ? $request->get('search_distance') : 0 ;
        $transport_details->save();

        $driver_vehicle_type = TransportVehicleType::query()->where('id',$transport_details->vehicle_type_id)->first();
        if($driver_vehicle_type == NULL){
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.9'),
                "message_code" => 9,
            ]);
        }

        $general_settings = request()->get("general_settings");
        if ($general_settings == Null) {
            return response()->json([
                "status" => 1,
                "message" => __('user_messages.372'),
                "message_code" => 372,
            ]);
        }

        return response()->json([
            "status" => 1,
            "message" => __('user_messages.1'),
            "message_code" => 1,
            "service_id" => $driver_vehicle_type->service_id,
            "search_radius" => $search_radius,
            "search_distance_filter" => $transport_details->search_distance_filter + 0,
            "driver_current_status" => $user_check->driver_current_status,
            "is_hail_ride" => $driver_vehicle_type->service_id != 4 ? $general_settings->is_hail_ride : 0,
            "cash_payment" => $general_settings->cash_payment != NUll ? $general_settings->cash_payment: 0,
            "online_payment" => $general_settings->card_payment != NUll ? $general_settings->card_payment: 0,
            "wallet_payment" => $general_settings->wallet_payment != NUll ? $general_settings->wallet_payment: 0,
            "accept_transport" => (int) ($transport_details->accept_transport ?? 1),
            "accept_delivery" => (int) ($transport_details->accept_delivery ?? 0),
            "accept_encomiendas" => (int) ($transport_details->accept_encomiendas ?? ($transport_details->accept_delivery ?? 0)),
        ]);
    }

    public function postUpdateDriverAvailabilityModes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "accept_transport" => "required|in:0,1",
            "accept_delivery" => "required|in:0,1",
            "accept_encomiendas" => "required|in:0,1",
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
        if ((int) $request->get('accept_transport') === 0
            && (int) $request->get('accept_delivery') === 0
            && (int) $request->get('accept_encomiendas') === 0) {
            return response()->json([
                "status" => 0,
                "message" => "Select at least one request type",
                "message_code" => 9,
            ]);
        }
        $transport_details = TransportDriverDetails::query()->where('user_id', $request->get('user_id'))->first();
        if ($transport_details == null) {
            return response()->json([
                "status" => 5,
                "message" => __('driver_messages.5'),
                "message_code" => 5,
            ]);
        }
        $vehicleType = TransportVehicleType::query()->find((int) ($transport_details->vehicle_type_id ?? 0));
        $vehicleServiceId = (int) ($vehicleType->service_id ?? 0);
        $transport_details->accept_transport = (int) $request->get('accept_transport');
        $transport_details->accept_delivery = (int) $request->get('accept_delivery');
        $transport_details->accept_encomiendas = (int) $request->get('accept_encomiendas');
        if ($vehicleServiceId === 7) {
            $transport_details->accept_transport = 1;
            $transport_details->accept_delivery = 0;
            $transport_details->accept_encomiendas = 0;
        } elseif ($vehicleServiceId === 4) {
            $transport_details->accept_transport = 0;
            if ($transport_details->accept_delivery === 0 && $transport_details->accept_encomiendas === 0) {
                return response()->json([
                    "status" => 0,
                    "message" => "Delivery drivers must accept envíos or encomiendas",
                    "message_code" => 9,
                ]);
            }
        } elseif (in_array($vehicleServiceId, ServiceCatalogHelper::DELIVERY_CAPABLE_TRANSPORT_SERVICE_IDS, true)) {
            // Transport drivers always accept transport; toggles only for envíos/encomiendas.
            $transport_details->accept_transport = 1;
        }
        $transport_details->save();
        return response()->json([
            "status" => 1,
            "message" => __('driver_messages.1'),
            "message_code" => 1,
            "accept_transport" => $transport_details->accept_transport,
            "accept_delivery" => $transport_details->accept_delivery,
            "accept_encomiendas" => (int) ($transport_details->accept_encomiendas ?? ($transport_details->accept_delivery ?? 0)),
        ]);
    }

    public function postHailRideBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "service_id"=>"required|numeric",
            "offered_fare" => "required",
            "estimated_time" => "required",
            "total_distance" => "required",
            "address_list" => "required",
            "other_user_name"=>"nullable",
            "other_user_contact_number"=>"nullable",
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

        $settings = request()->get('general_settings');
        if ($settings == Null) {
            return response()->json([
                "status" => 0,
                //"message" => "something went to wrong!",
                "message" => __('user_messages.9'),
                "message_code" => 9,
            ]);
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

        if($this->adminClass->is_in_restricted_area($points_polygon,$restricted_lat,$restricted_long,$address_lat,$address_long)){
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

        $get_vehicle_service = VehicleService::query()->where('id', $request->get('service_id'))->where('status',1)->first();
        if ($get_vehicle_service == Null) {
            return response()->json([
                "status" => 0,
                'message' => __('user_messages.9'),
                "message_code" => 9,
            ]);
        }

        $get_driver_detail = TransportDriverDetails::query()
            ->select('transport_driver_details.*','users.first_name as driver_name','users.device_token','users.login_device','users.language')
            ->join('users','users.id','=','transport_driver_details.user_id')
            ->where('transport_driver_details.user_id',$request->get('user_id'))->first();

        $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_check);

        $amount = round($request->get('offered_fare') / $currency,2);

        $service_settings = ServiceSettings::query()->first();
        if ($service_settings != Null) {
            $admin_commission = round(($amount * $service_settings->admin_hail_commission) / 100,2);
        } else {
            $admin_commission = 0;
        }

        if($settings->auto_settle_wallet == 1){
            $get_last_transaction = UserWalletTransaction::query()
                ->where('wallet_provider_type', "=", 0)
                ->where('user_id', "=", $request->get('user_id'))
                ->orderBy('id', 'desc')
                ->first();
            if ($get_last_transaction != Null) {
                $last_amount = $get_last_transaction->remaining_balance;
            } else {
                $last_amount = 0;
            }

            if($last_amount <= $admin_commission){
                return response()->json([
                    'status' => 0,
                    'message' => __('driver_messages.339',['currency_code' => $user_currency->symbol,'amount' => $admin_commission]),
                    'message_code' => 339,
                ]);
            }
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

                    if ($this->adminClass->is_in_restricted_area($area_points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)) {
                        $area_id = $get_area->id;
                        break;
                    }
                }
            }
        }

        $ride = new TransportRideBook();
        $ride->user_id = NULL;
        $ride->area_id = $area_id;
        $ride->vehicle_type_id = $get_driver_detail->vehicle_type_id;
        $ride->vehicle_service_id = $get_vehicle_service->id;
        $ride->vehicle_cost_for_km = $get_vehicle_service->cost_for_km;
        $ride->ride_no = $ride->generateRideNo();
        $ride->otp = NULL;
        $ride->user_name = NULL;
        $ride->pickup_datetime = date('Y-m-d H:i:s');
        $ride->pickup_address = $new_address_list[0]['address'];
        $ride->pickup_lat = $new_address_list[0]['address_lat'];
        $ride->pickup_long = $new_address_list[0]['address_long'];
        $ride->destination_address = $new_address_list[$address_count - 1]['address'];
        $ride->destination_latlong = $new_address_list[$address_count - 1]['address_lat'] . ',' . $new_address_list[$address_count - 1]['address_long'];
        $ride->min_bargain_amt = 0;
        $ride->total_pay = $amount;
        $ride->offered_price = $amount;
        $ride->total_distance = $request->get('total_distance');
        $ride->eta = $request->get('estimated_time');
        $ride->driver_name = $get_driver_detail->driver_name;
        $ride->driver_id = $request->get('user_id');
        $ride->admin_commission = $admin_commission;
        $ride->driver_amount = $ride->total_pay - $admin_commission;
        $ride->payment_type = 1;
        $ride->payment_status = 0;
        $ride->status = 5;
        $ride->is_hail = 1;
        $date = new \DateTime("now", new \DateTimeZone(config('app.timezone')) );
        $ride->retry_time = $date->format('Y-m-d H:i:s');
        $ride->other_user_name = $request->get('other_user_name') != NULL ? $request->get('other_user_name') : NULL;
        $ride->other_user_contact_number = $request->get('other_user_contact_number') != NULL ? $request->get('other_user_contact_number') : NULL;
        $ride->save();

        $provider_running_service = new ProviderUserRunningService();
        $provider_running_service->provider_id = $request->get('user_id');
        $provider_running_service->user_id = 0;
        $provider_running_service->booking_id = $ride->id;
        $provider_running_service->save();

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
        ]);
    }

    public function postDriverAcceptRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => ApiValidationRules::ACCESS_TOKEN,
            "ride_id" => "required|numeric",
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

        $ride = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();

        if ($ride != Null) {
            $general_settings=request()->get('general_settings');
            $currency = \App\Support\UserCurrencyResolver::ratioForUser($user_check);
            $service_setting = ServiceSettings::query()->select('admin_commission','driver_timeout')->first();
            if($general_settings->auto_settle_wallet == 1){
                //get wallet balance
                $last_amount = $this->notificationClass->getWalletBalance($request->get('user_id'));
                // admin commission of user offered price
                $commissionPercent = VehicleCommissionHelper::resolvePercent(
                    (int) $ride->vehicle_service_id,
                    $ride->delivery_variant ?? null
                );
                $commissionRate = $commissionPercent / 100;
                $vatRate = ((float) ($general_settings->vat_rate_on_commission ?? 19)) / 100;
                $admin_commission_user_offered = ($ride->offered_price * $commissionRate * (1 + $vatRate));
                if ($last_amount < $admin_commission_user_offered) {
                    $amount = $admin_commission_user_offered;
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.339',['amount' => round($amount*$currency,2),'currency_code' => $user_currency->symbol]),
                        "message_code" => 339
                    ]);
                }
            }

            if ($ride->status == 0) {
                if ((int) ($ride->driver_id ?? 0) > 0
                    && (int) $ride->driver_id !== (int) $request->get('user_id')) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.23'),
                        "message_code" => 23,
                    ]);
                }

                if ((int) ($ride->is_auto_accept ?? 0) !== 1) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('user_messages.326'),
                        "message_code" => 326,
                    ]);
                }

                $driver_details = TransportDriverDetails::query()
                    ->select('transport_driver_details.*', 'transport_vehicle_type.service_id')
                    ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
                    ->where('transport_driver_details.user_id', $request->get('user_id'))
                    ->first();
                if ($driver_details === null
                    || ! \App\Helpers\RideDriverEligibilityHelper::driverCanServePendingRide($driver_details, (int) $request->get('ride_id'))) {
                    return response()->json([
                        "status" => 0,
                        "message" => __('driver_messages.343'),
                        "message_code" => 343,
                    ]);
                }

                return $this->notificationClass->driverDirectAcceptedTransportRequestNotification($request->get('ride_id'), $request->get('user_id'));
            } elseif ($ride->status == 4) {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.24'),
                    "message_code" => 24,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => __('driver_messages.23'),
                    "message_code" => 23,
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    public function postDriverStartRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "access_token" => ApiValidationRules::ACCESS_TOKEN,
                "ride_id" => "required|numeric",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $driver_id = $request->get('user_id');
        $driver_check = $this->userClassapi->checkDriverRegisterAllow($driver_id);
        if ($failed = $this->userClassapi->authJsonResponse($driver_check)) {
            return $failed;
        }

        $driver_currency = \App\Support\UserCurrencyResolver::forCurrency($driver_check->currency);
        if ($driver_currency == Null) {
            $driver_currency = WorldCurrency::query()->where('default_currency', 1)->first();
        }
        $currency = $driver_currency->ratio;

        $ride = TransportRideBook::query()->where('id', $request->get('ride_id'))->first();
        if ($ride != Null) {
            if ($ride->status == 1 || $ride->status == 2) {
                if ($ride->status == 10) {
                    return response()->json([
                        "status" => 0,
//                            "message" => "Sorry, Your ride request is failed",
                        "message" => __('driver_messages.25'),
                        "message_code" => 25,
                    ]);
                }

                if ($ride->ride_type == 1 || $ride->pickup_datetime != Null) {
                    $service_setting = ServiceSettings::query()->first();
                    $pickup_date_time = (date('Y-m-d H:i:s', strtotime('-'.$service_setting->schedule_ride_start_before.' minutes', strtotime($ride->pickup_datetime))));
                    $current_date_time = (date('Y-m-d H:i:s'));
                    if ($current_date_time >= $pickup_date_time) {

                    } else {
                        return response()->json([
                            "status" => 0,
//                                "message" => "You can start the service before 60 minutes of requested time.",
                            "message" => __('driver_messages.118',['time'=>$service_setting->schedule_ride_start_before]),
                            "message_code" => 118,
                        ]);
                    }
                }

                $get_driver_id = TransportDriverDetails::query()
                    ->select('transport_driver_details.*','users.first_name as driver_name','users.email','transport_vehicle_type.service_id')
                    ->join('transport_vehicle_type','transport_vehicle_type.id','=','transport_driver_details.vehicle_type_id')
                    ->join('users','users.id','=','transport_driver_details.user_id')
                    ->where('user_id',$request->get('user_id'))->first();

                if ($get_driver_id != Null) {
                    if ($get_driver_id->user_id == $ride->driver_id) {
                        $user = User::query()->where('id', $ride->user_id)->first();
                        if ($user == Null) {
                            return response()->json([
                                "status" => 0,
//                                        "message" => "user not found",
                                "message" => __('driver_messages.9'),
                                "message_code" => 9,
                            ]);
                        }
                        $user_profile_url = url('/assets/images/profile-images/customer');
                        $ride_details = TransportRideBook::query()->select('user_ride_booking.id as ride_id',
                            'user_ride_booking.ride_no as booking_no',
                            DB::raw("(CASE WHEN users.avatar != '' THEN (CASE WHEN CHAR_LENGTH(users.avatar) >= 25 THEN users.avatar ELSE concat('$user_profile_url','/',users.avatar) END) ELSE '' END) as user_profile_image"),
                            DB::raw("(concat(users.first_name,' ',users.last_name)) as user_name"),
                            'users.contact_number',
                            'user_ride_booking.pickup_address as pickup_address',
                            'user_ride_booking.pickup_lat as pickup_lat',
                            'user_ride_booking.pickup_long as pickup_long',
                            'user_ride_booking.destination_address as destination_address',
                            'user_ride_booking.destination_latlong as destination_latlong',
                            'user_ride_booking.pickup_datetime as pickup_datetime',
                            'user_ride_booking.total_pay as total_amount',
                            'user_ride_booking.payment_type as payment_type',
                            'user_ride_booking.is_way_point as is_way_point',
                            'user_ride_booking.way_point_status as way_point_status',
                            'user_ride_booking.status as ride_status')
                            ->join('users', 'users.id', '=', 'user_ride_booking.user_id')
                            ->whereNull('users.deleted_at')
                            ->where('user_ride_booking.id', $ride->id)->first();
                        if ($ride_details != Null) {
                            if ($ride->ride_type == 0) {
                                $running_service = ProviderUserRunningService::query()
                                    ->where('provider_id', $driver_id)
                                    ->where('user_id', $ride->user_id)
                                    ->where('booking_id', $ride->id)
                                    ->first();
                                if ($running_service == Null) {
                                    $provider_running_service = new ProviderUserRunningService();
                                    $provider_running_service->provider_id = $driver_id;
                                    $provider_running_service->user_id = $ride->user_id;
                                    $provider_running_service->booking_id = $ride->id;
                                    $provider_running_service->save();
                                }
                            }
                            $address_list = [];
                            $destinationParts = array_map('trim', explode(',', (string) ($ride_details->destination_latlong ?? ''), 2));
                            $destLat = $destinationParts[0] ?? '0';
                            $destLng = $destinationParts[1] ?? '0';
                            $address_list[] = [
                                "address" => $ride_details->pickup_address,
                                "address_lat" => trim($ride_details->pickup_lat),
                                "address_long" => trim($ride_details->pickup_long)
                            ];

                            if ($ride_details->is_way_point == 1) {
                                $ride_way_point = UserRideWayPoint::query()->where('ride_id', $ride_details->ride_id)->first();
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
                                            "address" => $ride_way_point->way_point_1,
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
                                "address_lat" => $destLat,
                                "address_long" => $destLng,
                            ];

                            $ride->status = 3;
                            $ride->save();
                            $this->notificationClass->userTransportNotification($ride->id, $user->device_token, 3, $user->login_device, $user->language);

                            $ride_details_arr = [
                                "ride_id" => $ride_details->ride_id,
                                "user_id" => $user->id,
                                "user_fcm_token" => $user->device_token,
                                "booking_no" => $ride_details->booking_no,
                                "user_profile_image" => $ride_details->user_profile_image,
                                "user_name" => $ride_details->user_name,
                                "contact_number" => $ride_details->contact_number,
                                "address_list" => $address_list,
                                "pickup_datetime" => $ride_details->pickup_datetime,
                                "total_amount" => \App\Helpers\TripAmountHelper::resolveForCurrency($ride_details, (float) $currency),
                                "payment_type" => $ride_details->payment_type,
                                "ride_status" => 3,
                                "way_point_status" => $ride_details->way_point_status,
                            ];

                            return response()->json([
                                "status" => 1,
//                                        "message" => "success!",
                                "message" => __('driver_messages.1'),
                                "message_code" => 1,
                                //"driver_current_status" => $driver_service->current_status,
                                "ride_details" => $ride_details_arr,
                            ]);

                        } else {
                            return response()->json([
                                "status" => 0,
//                                        "message" => "ride details not found!",
                                "message" => __('driver_messages.26'),
                                "message_code" => 26,
                            ]);
                        }
                    } else {
                        return response()->json([
                            "status" => 0,
                            "message" => __('driver_messages.9'),
                            "message_code" => 9,
                        ]);
                    }
                } else {
                    return response()->json([
                        "status" => 0,
//                            "message" => "ride details not found!",
                        "message" => __('driver_messages.26'),
                        "message_code" => 26,
                    ]);
                }
            }  elseif ($ride->status == 4) {
                return response()->json([
                    "status" => 0,
//                        "message" => "Sorry, Your ride request is cancelled by user",
                    "message" => __('driver_messages.24'),
                    "message_code" => 24,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
//                        "message" => "something went to wrong!",
                    "message" => __('driver_messages.9'),
                    "message_code" => 9,
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
//                    "message" => "ride not found!",
                "message" => __('driver_messages.26'),
                "message_code" => 26,
            ]);
        }
    }

    private function storeDriverVehiclePhoto(Request $request, string $field, ?string $existingFile = null): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }
        $file = $request->file($field);
        if ($existingFile && \File::exists(public_path('/assets/images/provider-vehicle-image/' . $existingFile))) {
            \File::delete(public_path('/assets/images/provider-vehicle-image/' . $existingFile));
        }
        $fileNew = rand(1, 9) . date('siHYdm') . rand(1, 9) . '_' . $field . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('/assets/images/provider-vehicle-image/'), $fileNew);

        return $fileNew;
    }

    private function driverRegistrationRequiresPlate(Request $request): bool
    {
        $deliveryVariant = $request->filled('delivery_variant')
            ? (string) $request->get('delivery_variant')
            : null;

        if ($deliveryVariant === 'bicycle') {
            return false;
        }

        $typeId = (int) $request->get('vehicle_type_id');
        if ($typeId > 0 && Schema::hasColumn('transport_vehicle_type', 'requires_plate')) {
            $flag = DB::table('transport_vehicle_type')->where('id', $typeId)->value('requires_plate');
            if ($flag !== null) {
                return (int) $flag === 1;
            }
        }

        return true;
    }

}
