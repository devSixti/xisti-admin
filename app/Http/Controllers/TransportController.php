<?php

namespace App\Http\Controllers;

use App\Classes\AdminClass;
use App\Classes\NotificationClass;
use App\Http\Requests\ProviderStoreRequest;
use App\Http\Requests\TransportVehicleTypeRequest;

use App\Models\AdminModule;
use App\Models\AdminPermission;
use App\Models\CashOut;
use App\Models\LanguageLists;
use App\Models\ProviderBankDetails;
use App\Models\ProviderDocuments;
use App\Models\ProviderUserRunningService;
use App\Models\RequiredDocuments;
use App\Models\ServiceSettings;
use App\Models\TransportDriverDetails;
use App\Models\TransportRatings;
use App\Models\TransportRideBook;
use App\Models\TransportVehicleType;
use App\Models\User;
use App\Models\UserRunningRide;
use App\Models\UserReferHistory;
use App\Models\UserWalletTransaction;
use App\Models\VehicleService;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use App\Models\TransportCourierDetails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;

class TransportController extends Controller
{
    private $adminClass;
    private $courier_service = 4;

    private $notificationClass;
    private $transport_service_id_array = [1, 2];
    private $courier_service_id_array = [4];
    private $all = [0, 4, 9, 10];
    private $available = [0, 4, 9, 10];
    private $ride_start = [5, 6, 7, 8];
    private $reached = [3];
    private $enroute = [1, 2,];
    private $transport_vehicle_id_array =  [10, 14];
    private $is_restricted = 0;

    public function __construct(AdminClass $adminClass, NotificationClass $notificationClass)
    {
        $this->adminClass = $adminClass;
        $this->notificationClass = $notificationClass;

        $this->middleware( function ($request, $next) {
            $is_restrict_admin = $request->get('is_restrict_admin');
            $this->is_restricted = $is_restrict_admin;
            return $next($request);
        });
    }

    public function getTransportProviderListNew(Request $request){

        $wallet_payment = 0;
        $general_settings = request()->get("general_settings");
        if ($general_settings != Null){
            $wallet_payment = $general_settings->wallet_payment;
        }

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $rowperpage = (isset($rowperpage) && $rowperpage > 0) ? $rowperpage : 25;
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $status = $request->get('status');
        $service_category_id = $request->get('service_category_id');

        $provider_lists = TransportDriverDetails::query()->select(
            DB::raw("users.first_name as driver_name"),
            'users.id as provider_id',
            'users.email',
            'users.login_type',
            'users.country_code',
            'users.contact_number',
            'users.created_at',
            'users.app_version as provider_app_version',
            'transport_vehicle_type.name as vehicle_type_name', 'transport_vehicle_type.icon_name as type_icon',
            'transport_driver_details.id as driver_details_id', 'transport_driver_details.vehicle_company',
            'transport_driver_details.plat_no', 'transport_driver_details.model_name', 'transport_driver_details.model_year',
            'transport_driver_details.vehicle_color', 'transport_driver_details.rating', 'transport_driver_details.total_request',
            'transport_driver_details.total_completed', 'transport_driver_details.total_cancelled',
        )
            ->join('users', 'users.id', 'transport_driver_details.user_id')
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
            ->where('users.status','=',1)
            ->where('users.is_driver_type','=',1)
            ->where('users.is_driver_status', '=', $status)
            ->whereNull('users.deleted_at');
        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $provider_lists->where('users.area_id', request()->get("admin_city_id"));
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/
        $totalRecords = $provider_lists->count();
        if ($searchValue != "") {
            $totalRecordswithFilter = $provider_lists->where(function ($query) use ($searchValue) {
                $query->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.email', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.contact_number', 'like', '%' . $searchValue . '%')
                    ->orWhere('transport_vehicle_type.name', 'like', '%' . $searchValue . '%');
            })->count();
        } else {
            $totalRecordswithFilter = $provider_lists->count();
        }

        $records = $provider_lists;
        if ($searchValue != "") {
            $records = $records->where(function ($query) use ($searchValue) {
                $query->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.email', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.contact_number', 'like', '%' . $searchValue . '%')
                    ->orWhere('transport_vehicle_type.name', 'like', '%' . $searchValue . '%');
            });
        }
        if ($columnName != 'no'){
            $records = $records->orderBy($columnName, $columnSortOrder);
        }else{
            $records = $records->orderBy('transport_driver_details.id', 'desc');
        }
        $records = $records->skip($start)->take($rowperpage)->get();

        $data_arr = array();
        $i = 1;
        foreach ($records as $record) {
            $provider_app_version = ($record->provider_app_version != Null) ? $record->provider_app_version : 0;
            if ($record->type_icon != Null) {
                $icon = '<img id="vehicle_type_icon_' . $record->driver_details_id . '"
                               src="' . asset('/assets/images/service-category/transport-service-type/' . $record->type_icon) . '"
                               width="26" height="26">';
            } else {
                $icon = '----';
            }
            $vehical_type = '<span title="Vehicle Details" class="md-trigger md-trigger-vehicle"
                                data-modal="modal-1" data-toggle="tooltip"
                                model_type="0"
                                driver_name="' . $record->driver_name . '"
                                vehicle_type_name="' . $record->vehicle_type_name . '"
                                vehicle_company="' . $record->model_name . ' ' . $record->vehicle_company . '"
                                plat_no="' . $record->plat_no . '"
                                model_year="' . $record->model_year . '"
                                color="' . $record->vehicle_color . '"
                                url="' . route('get:admin:edit_transport_provider_vehicle_details', [$record->driver_details_id]) . '"
                                vehicle_id="' . $record->driver_details_id . '">
                                ' . $icon . '
                             </span>';

            $driver_name = '<span title="Vehicle Details" class="md-trigger" data-modal="modal-2" data-toggle="tooltip" model_type="1"
                                driver_name="' . $record->driver_name . '"
                                total_request="' . $record->total_request . '"
                                total_completed="' . $record->total_completed . '"
                                total_cancelled="' . $record->total_cancelled . '"
                            >' . ucwords($record->driver_name) . '</span>';


            $rating = '<a href="' . route('get:admin:transport_provider_review_list', ['driver_id' => $record->driver_details_id]) . '" class="render_link" data-toggle="tooltip" data-placement="top" title="Rating List">
                            <span class="icon-list-demo">
                                <i class="fa fa-star"></i>' . round($record->rating,2) . '
                            </span>
                        </a>';


            $rides = '';
            $driver_wallet_balance_html = "";
            if ((isset($status) && $status == 1) || (isset($status) && $status == 2)) {
                $icon = ' <i class="fa fa-car"></i>';
                $title = 'Ride';
                $rides = '<a href="' . route('get:admin:transport_provider_ride_list', ['driver_id' => $record->provider_id]) . '"
                            class="render_link"
                            title="' . $title . ' List">
                                <span class="icon-list-demo">
                                   ' . $icon . '
                                </span>
                          </a>';
                if($wallet_payment == 1) {
                    //code check wallet
                    $user_wallet = UserWalletTransaction::query()->select('remaining_balance')->where('user_id', $record->provider_id)->where('wallet_provider_type', 0)->orderBy('id', 'desc')->first();
                    if ($user_wallet != Null) {
                        $user_wallet_balance = round($user_wallet->remaining_balance,2);
                    } else {
                        $user_wallet_balance = 0;
                    }
                    $driver_wallet_balance_html = '<span id="change_wallet_' . $record->provider_id . '">' . $user_wallet_balance . '</span><a href="' . route('get:admin:provider_wallet_transaction', ['id' => $record->provider_id]) . '" providerid="' . $record->provider_id . '" style="margin: 0 7px;">
                            <img src="' . asset('/assets/images/template-images/wallet-history3.png') . '" style="width:25px; height: 25px;" title="Wallet Transaction">
                        </a>
                        <a style="border: 1px solid Green; border-radius: 5px; font-size: 16px; font-weight: bolder; color: green; padding: 0 5px;cursor: pointer" class="md-trigger-2 text-c-orenge"
                              data-modal="modal-4" data-toggle="tooltip" providerid="' . $record->provider_id . '"> <i class="fa fa-plus" aria-hidden="true"></i> / <i class="fa fa-minus" aria-hidden="true"></i> </a>';
                }
            }

            $document = '<a href="' . route('get:admin:transport_provider_document', ['user_id' => $record->provider_id]) . '"
                            class="render_link" data-toggle="tooltip"
                            data-placement="top" title="Document List">
                            <span class="icon-list-demo">
                                <i class="fa fa-file-text"></i>
                            </span>
                        </a>';
            if ($status == 0) {
                $status_html = '<a class="render_link">
                                <img src="' . asset('/assets/images/template-images/thumbs-up.png') . '"
                                     style="width:20px; height: 20px;"
                                     data-toggle="tooltip" class="approve approve_element_' . $record->provider_id . '"
                                     provider_service_id="' . $record->provider_id . '"
                                     data-placement="top" title="Approve">
                          </a>
                          <a class="render_link">
                                <img src="' . asset('/assets/images/template-images/thumb-down.png') . '"
                                    style="width:20px; height: 20px;"
                                    data-toggle="tooltip" class="reject reject_element_' . $record->provider_id . '"
                                    provider_service_id="' . $record->provider_id . '"
                                    data-placement="top" title="Reject">
                          </a>';
            } elseif ($status == 1) {
                $status_html = '<span class="toggle"><label>
                                <input name="manual_assign"
                                    class="form-control store_status block block_element_' . $record->provider_id . '"
                                    type="checkbox" checked
                                    provider_service_id="' . $record->provider_id . '"><span
                                    class="button-indecator"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Active"></span></label>
                           </span>';

            } elseif ($status == 2) {
                $status_html = '<span class="toggle"><label>
                                <input name="manual_assign"
                                    class="form-control store_status unblock unblock_element_' . $record->provider_id . '"
                                    type="checkbox" checked
                                    provider_service_id="' . $record->provider_id . '"><span
                                    class="button-indecator"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Active"></span></label>
                           </span>';
            } else {
                $status_html = '<span class="toggle"><label>
                                <input name="manual_assign"
                                    class="form-control store_status approve"
                                    type="checkbox" checked
                                    provider_service_id="' . $record->provider_id . '"><span
                                    class="button-indecator"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Active"></span></label>
                           </span>';
            }
            $status_html .= '<a href="' . route('get:admin:edit_transport_service_driver', [$record->driver_details_id]) . '"
                            class="render_link edit">
                            <img src="' . asset('/assets/images/template-images/writing-1.png') . '"
                                style="width:20px; height: 20px;"
                                data-toggle="tooltip"
                                data-placement="top" title="Edit">
                        </a>';


            if (isset($status) && in_array($status, [0, 2, 3])) {
                $register_name = date("d-M Y h:i A", strtotime($record->created_at));
            } else {
                $register_name = '';
            }
            $data_arr[] = array(
                "DT_RowId" => "hide_" . $record->provider_id,
                "no" => $i,
                "vehicle_type" => $vehical_type,
                "driver_name" => $driver_name,
                "email" => User::Email2Stars($record->email),
                "contact_number" => User::ContactNumber2Stars($record->country_code . $record->contact_number),
                "rating" => $rating,
                'rides' => $rides,
                'driver_wallet_balance_html' => $driver_wallet_balance_html,
                'documents' => $document,
                'sign_up_time' => $register_name,
                'provider_app_version' => $provider_app_version,
                'actions' => $status_html,
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return json_encode($response);
    }

    //get Transport Un-approved Provider List
    public function getTransport_Blocked_ProviderList(Request $request,$status)
    {
        if ($status == "approved") {
            $status = 1;
        } elseif ($status == "un-approved") {
            $status = 0;
        } elseif ($status == "blocked") {
            $status = 2;
        } elseif ($status == "rejected") {
            $status = 3;
        } else {
            Session::flash('error', 'Category provider list not found!');
            return redirect()->back();
        }

        $provider_list_check = TransportDriverDetails::query()
            ->select(
                DB::raw("users.first_name as driver_name"), 'users.id as provider_id', 'users.email', 'users.contact_number',
                'users.created_at',
                'users.status',
                'transport_driver_details.id as driver_details_id',
                'transport_driver_details.rating', 'transport_driver_details.total_request',
                'transport_driver_details.total_completed', 'transport_driver_details.total_cancelled',
                'transport_driver_details.vehicle_company', 'transport_driver_details.plat_no',
                'transport_driver_details.model_name', 'transport_driver_details.model_year',
                'transport_driver_details.vehicle_color',
                'transport_vehicle_type.name as vehicle_type_name',
                'transport_vehicle_type.icon_name as type_icon',
            )
            ->join('users', 'users.id', 'transport_driver_details.user_id')
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
            ->where('users.is_driver_type',1)
            ->where('users.is_driver_status',$status)
            ->whereNull('users.deleted_at');

        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $provider_list_check->where('users.area_id', request()->get("admin_city_id"));
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/
            $provider_lists = $provider_list_check
                ->orderBy('transport_driver_details.id',  'desc')
                ->get();

        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.transport_provider_list', compact( 'status',  'provider_lists'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.transport_provider_list', compact( 'status',  'provider_lists'));
    }


    //get Ajax Transport Provider Status Change
    public function getUpdateTransportProviderStatus(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        $request_for = $request->get('request_for');
        if ($id == Null || $request_for == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found1'
            ]);
        }


        $driver_details = User::query()->where('id', $id)->first();
        if ($driver_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found3'
            ]);
        }
        if ($request_for == 1) {
            $driver_details->is_driver_status = 1;
            $driver_details->save();

            $general_settings = request()->get("general_settings");
            if ($general_settings !=  Null) {
                if ($general_settings->send_mail == 1) {
                    $driver_name = ($driver_details->first_name !="")?$driver_details->first_name : "";
                    try{
                        $mail_type = "driver_account_approve";
                        $to_mail = $driver_details->email;
                        $subject = "Your Pending Account Approved By Admin ";
                        $disp_data = array("##driver_name##"=>$driver_name );
                        $mail_return_data = $this->notificationClass->sendMail($subject,$to_mail,$mail_type,$disp_data);
                    }
                    catch (\Exception $e){}
                }
            }

        } elseif ($request_for == 2) {
            $transport_driver_details=TransportDriverDetails::query()
                ->join('users','users.id','=','transport_driver_details.user_id')
                ->where('transport_driver_details.user_id',$driver_details->id)->first();

            if($driver_details->fix_user_show == 1){
                return response()->json([
                    "success" => false,
                    "message" => "Sorry,You cannot block this user"
                ]);
            }

            $user_running_ride = TransportRideBook::query()->where('driver_id', '=', $transport_driver_details->id)->whereNotIn('status', [4, 9, 10])->count();
            if ($user_running_ride > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Sorry, Currently the ride of this driver is running so you can't block the account at this time. Try Later!"
                ]);
            }

            $driver_details->is_driver_status = 2;
            $driver_details->active_mode = 1;
            $driver_details->device_token = Null;
            $driver_details->save();

            $general_settings = request()->get("general_settings");
            if ($general_settings !=  Null) {
                if ($general_settings->send_mail == 1) {
                    $driver_name = $driver_details->first_name;
                    try {
                        $mail_type = "driver_account_block";
                        $to_mail = $driver_details->email;
                        $subject = "Your Account has been Block by Admin";
                        $disp_data = array("##driver_name##" => $driver_name);
                        $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                    }
                    catch (\Exception $e) {}
                }
            }
        } elseif ($request_for == 3) {
            $driver_details->is_driver_status = 3;
            $driver_details->rejected_reason = $request->get('rejected_reason');
            $driver_details->device_token = Null;
            $driver_details->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found4'
            ]);
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function getTransportRideList(Request $request){
        $status_check = $request->get('status_check');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $rowperpage = (isset($rowperpage) && $rowperpage > 0) ? $rowperpage : 25;
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $ride_lists_check = TransportRideBook::query()
            ->select('user_ride_booking.id', 'user_ride_booking.ride_no',
                'user_ride_booking.total_pay', 'user_ride_booking.status', 'user_ride_booking.pickup_address',
                'user_ride_booking.destination_address',
                'user_ride_booking.user_name',
                'user_ride_booking.payment_type',
                'user_ride_booking.payment_status',
                'user_ride_booking.user_refund_status',
                'user_ride_booking.driver_name',
                'user_ride_booking.ride_for_other',
                'user_ride_booking.is_hail',
                'user_ride_booking.other_user_name',
                'transport_vehicle_type.name as vehicle_name'
            )
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id');
        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $ride_lists_check->where('user_ride_booking.area_id', request()->get("admin_city_id"));
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/
        if ($status_check == "all") {
            $ride_lists = $ride_lists_check->whereIn('user_ride_booking.status', [1, 2, 3, 4, 5, 6,7, 8, 9, 10]);
            $status = "all";
        }
        elseif ($status_check == "scheduled") {
            $ride_lists = $ride_lists_check->whereIn('user_ride_booking.status', [2, 4, 5, 9])
                ->where('user_ride_booking.ride_type', '=', 1);
            $status = "scheduled";
        }
        elseif ($status_check == "pending") {
            $ride_lists = $ride_lists_check->where('user_ride_booking.status', '=', 0);
            $status = "pending";
        }
        elseif ($status_check == "cancelled") {
            $ride_lists = $ride_lists_check->where('user_ride_booking.status', '=', 4);
            $status = 4;
        }
        elseif ($status_check == "approved"){
            $ride_lists = $ride_lists_check->whereIn("user_ride_booking.status",[1,3]);
        }
        elseif ($status_check == "ongoing"){
            $ride_lists = $ride_lists_check->whereIN("user_ride_booking.status",[5,6,7,8]);
        }
        elseif ($status_check == "completed"){
            $ride_lists = $ride_lists_check->where("user_ride_booking.status",9);
        }
        else {
            $ride_lists = $ride_lists_check->whereIn('user_ride_booking.status', [1, 2, 4, 5, 9]);
            $status = "all";
        }

        $ride_list_data = $ride_lists;
        $totalRecords = $ride_lists->count();
        if($searchValue != ""){
            $totalRecordswithFilter = $ride_lists->where(function($query) use ($searchValue){
                $query->orWhere('user_ride_booking.ride_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('user_ride_booking.user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.driver_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.total_pay', 'like', '%' .$searchValue . '%');
            })->count();
        } else{
            $totalRecordswithFilter = $ride_lists->count();
        }

        $record = $ride_list_data;
        if($searchValue != ""){
            $record = $record->where(function($query) use ($searchValue){
                $query->orWhere('user_ride_booking.ride_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('user_ride_booking.user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.driver_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.total_pay', 'like', '%' .$searchValue . '%');
            });
        }

        if ($columnName != 'no'){
            $record = $record->orderBy($columnName, $columnSortOrder);
        }else{
            $record = $record->orderBy('user_ride_booking.id', 'desc');
        }

        $records = $record->skip($start)
            ->take($rowperpage)
            ->get();

        $i = 1;
        $data_arr = [];
        foreach ($records as $record){
            if($record->status == 0){
                $ride_status = "pending";
            } else if($record->status == 1 || $record->status == 2 || $record->status == 3){
                $ride_status = "approved";
            } else if($record->status == 4){
                $ride_status = "cancelled";
            } else if($record->status == 5 || $record->status == 6 || $record->status == 7 || $record->status == 8){
                $ride_status = "ongoing";
            } else if($record->status == 9){
                $ride_status = "completed";
            } else if($record->status == 10){
                $ride_status = "failed";
            }
            $ride_status_html = '<span class="'.$ride_status.'">'.$ride_status.'</span>';

            if($record->status == 3 || $record->status == 4){
                if($record->payment_type == 1 ){
                    $ride_refund_status_html = '<span class="approved">N/A</span>';
                }else{
                    if($record->user_refund_status == 1){
                        $ride_refund_status_html = '<span class="completed">Completed</span>';
                    }else{
                        $ride_refund_status_html = '<span class="pending">Pending</span>';
                    }
                }
            }else{
                $ride_refund_status_html = '<span class="approved">N/A</span>';
            }

            if($record->payment_status == 1){
                $payment_status = '<span class="completed">Paid</span>';
            }else{
                $payment_status =  '<span class="pending">Pending</span>';
            }

            $transport_services = $this->transport_service_id_array;
            $courier_service = $this->courier_service_id_array;
            if(Auth::guard("admin")->check()){
                if(Auth::guard("admin")->user()->roles == 1 || Auth::guard("admin")->user()->roles == 4){

                        $ride_details_html = '<a href="'.route('get:admin:ride_details',[$record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';

                } elseif(Auth::guard("admin")->user()->roles == 2){
                    if(in_array($record->service_cat_id, $transport_services)){
                        $ride_details_html = '<a href="'.route('get:dispatcher:ride_details',[ $slug, $record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';
                    }
                } elseif(Auth::guard("admin")->user()->roles == 3){
                        $ride_details_html = '<a href="'.route('get:account:ride_details',[$record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';
                }
            }
            $address = '<i class="fa fa-map-marker text-success"></i>'.$record->pickup_address.'<br>
                        <i class="fa fa-map-marker text-danger"></i>'. $record->destination_address;

            if(!in_array($record['status'], [0,4,9,10])){
                $chat = '<a href="'.route('get:admin:get_order_wise_chat',[$record->id]).'" class="btn btn-primary btn-sm" style="width:15px;padding:8px 21px 8px 10px"><i class="fas fa-comment-dots"></i></a>';
            } else {
                $chat = '<span class="order-status"><span class="cancelled">N/A</span></span>';
            }

            $data_arr[] = array(
                "no" => $i,
                "ride_no" => $record->ride_no,
                "user_name" => ($record->ride_for_other == 1 || $record->is_hail == 1) ? $record->other_user_name : $record->user_name,
                "driver_name" => $record->driver_name,
                "vehicle_name" => $record->vehicle_name,
                "total_pay" => '<span class="currency"></span>'.$record->total_pay,
                'status' => '<span class="order-status">'.$ride_status_html.'</span>',
                'refund_status' => '<span class="order-status">'.$ride_refund_status_html.'</span>',
                'payment_status' => '<span class="order-status">'.$payment_status.'</span>',
                'chat' => $chat,
                'details'=>$ride_details_html,
                'address'=>$address
            );
            $i++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        return json_encode($response);
    }

    //get Transport Service Category Ride List
    public function getTransportServiceRideList($status_check, Request $request)
    {
        if ($status_check == "all") {
            $status = "all";
        } elseif ($status_check == "approved") {
            $status = "approved";
        }elseif ($status_check == "scheduled") {
            $status = "scheduled";
        } elseif ($status_check == "pending") {
            $status = "pending";
        } elseif ($status_check == "cancelled") {
            $status = "cancelled";
        } elseif ($status_check == "accepted"){
            $status = "accepted";
        } elseif ($status_check == "ongoing"){
            $status = "ongoing";
        } elseif ($status_check == "completed"){
            $status = "completed";
        }
        else {
            $status = "all";
        }

        $view = view('admin.pages.transport_services.transport_provider_ride_list', compact( 'status', 'status_check'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get Transport Provider Ride List
    public function getTransportProviderRideList(Request $request,$driver_id) {

        $driver_details = $driver_details_check = TransportDriverDetails::query()->select(DB::raw("users.first_name as driver_name"),)
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->whereNull('users.deleted_at')
            ->where('transport_driver_details.user_id', '=', $driver_id)
            ->whereNull('users.deleted_at')
            ->first();
        if ($driver_details == Null) {
            Session::flash('error', 'Provider ride not found!');
            return redirect()->back();
        }
        $driver_type =(($driver_details->store_id > 0)? 1 : 0);
        $status = "all";
        $status_check = "all";

        $view = view('admin.pages.transport_services.transport_single_provider_ride_list', compact('driver_id',  'driver_details',  'status', 'status_check'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get Transport Ride Details
    public function getTransportRideDetails($id, Request $request) {

        $ride_Details = TransportRideBook::query()->select(
            'user_ride_booking.id', 'user_ride_booking.ride_no','user_ride_booking.vehicle_service_id',
            'user_ride_booking.user_id', 'user_ride_booking.driver_id',
            'user_ride_booking.user_name', 'user_ride_booking.driver_name',
            'user_ride_booking.pickup_datetime', 'user_ride_booking.destination_datetime',
            'user_ride_booking.pickup_address', 'user_ride_booking.destination_address',
            'user_ride_booking.vehicle_cost_for_km',
            'user_ride_booking.eta',
            'user_ride_booking.total_distance_amount',
            'user_ride_booking.tip',
            'user_ride_booking.refer_discount',
            'user_ride_booking.sub_total',
            'user_ride_booking.offered_price',
            'user_ride_booking.created_at',
            'user_ride_booking.otp',
            'user_ride_booking.total_distance',
            'user_ride_booking.total_pay', 'user_ride_booking.payment_type', 'user_ride_booking.payment_status',
            'user_ride_booking.promo_code',
            'user_ride_booking.status',
            'user_ride_booking.user_refund_status',
            'user_ride_booking.cancel_charge',
            'user_ride_booking.refund_amount',
            'user_ride_booking.ride_type',
            'user_ride_booking.is_driver_reassign',
            'transport_vehicle_type.name as vehicle_type_name',
            'user_ride_booking.ride_for_other',
            'user_ride_booking.other_user_name',
            'user_ride_booking.other_user_contact_number',
            'user_ride_booking.is_way_point',
            'user_ride_booking.toll_charge',
            'user_ride_booking.is_hail',
            'user_ride_way_point_list.way_point_1 as way_point_1',
            'user_ride_way_point_list.way_point_2 as way_point_2',
            'user_ride_way_point_list.way_point_3 as way_point_3'
        )
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id')
            ->leftJoin('user_ride_way_point_list', 'user_ride_way_point_list.ride_id', '=', 'user_ride_booking.id')
            ->where('user_ride_booking.id', $id)->first();
        if ($ride_Details == Null) {
            return redirect()->back();
        }

        $user_details = User::query()->select('id', 'contact_number', 'country_code', 'email')->where('id', $ride_Details->user_id)->first();
        $driver_details = TransportDriverDetails::query()->select('transport_driver_details.id','users.first_name as driver_name', 'users.email', 'users.contact_number','users.country_code')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->whereNull('users.deleted_at')
            ->where('transport_driver_details.user_id', $ride_Details->driver_id)
            ->first();
        $courier_details = Null;
        if ($ride_Details != Null && $ride_Details->vehicle_service_id == 4) {
            $courier_details = TransportCourierDetails::query()->Select(
                'user_courier_service_details.recipient_name',
                'user_courier_service_details.recipient_contact_number',
                'user_courier_service_details.item_description',
                'user_courier_service_details.estimate_price',
            )->where('ride_id', $ride_Details->id)->first();
        }

        $grand_total = round($ride_Details->total_pay, 2);

        $view = view('admin.pages.transport_services.ride.manage_ride_details', compact( 'ride_Details', 'user_details', 'driver_details', 'grand_total','courier_details'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }


    //get Transport Provider Document
    public function getTransportProviderDocument($user_id, Request $request)
    {
        $driver_details = TransportDriverDetails::query()
            ->select('users.first_name','users.contact_number','transport_driver_details.plat_no',)
            ->join('users','users.id','=','transport_driver_details.user_id')
            ->where('users.id',$user_id)->first();
        $provider_documents[] = Null;
        $required_documents_lists = RequiredDocuments::query()->where('status', 1)->get();
        foreach ($required_documents_lists as $key => $required_document) {
            $document_details = ProviderDocuments::query()->where('user_id', '=', $user_id)->where('req_document_id', $required_document->id)->first();
                $provider_documents[$key] = $document_details;
        }
        $view = view('admin.pages.transport_services.transport_provider_document', compact( 'required_documents_lists', 'provider_documents','user_id','driver_details'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }


    //Transport Provider upload document
    public function postDriverTransportServiceDocument(Request $request)
    {
        $user_id = $request->get('user_id');
        $driver_details = TransportDriverDetails::where('user_id', $user_id)->first();
        if ($driver_details == Null) {
            return redirect()->back();
        }
        $get_document = RequiredDocuments::query()
            ->when($request->has('doc_id'), function ($query) use ($request) {
                $query->where('id', $request->get('doc_id'));
            }, function ($query) use ($request) {
                $query->where('id', $request->get('docs_id'));
            })
            ->where('status', 1)
            ->first();

        if ($get_document != Null) {
            $find_document = ProviderDocuments::where('user_id', $driver_details->user_id)->where('req_document_id', $get_document->id)->first();

            if ($find_document != Null) {
                if ($request->hasFile('document_file')) {
                    if (\File::exists(public_path('/assets/images/provider-documents/' . $find_document->file_name))) {
                        \File::delete(public_path('/assets/images/provider-documents/' . $find_document->file_name));
                    }
                    $image = $request->file('document_file');
                    $destinationPath = public_path('/assets/images/provider-documents/');

                    $image_extension = $image->extension();
                    $file_new = rand(1, 9) . date('siHYdm') . rand(1, 9) . '.' . $image->getClientOriginalExtension();

                    if ($image_extension != "pdf" && $image_extension != "doc" && $image_extension != "docx"){
                        $img = Image::read($image->getRealPath());
                        $img->orient();
                        $img->resize(500, 500, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPath . $file_new);
                    } else {
                        $image->move($destinationPath, $file_new);
                    }

                    $find_document->req_document_id = $get_document->id;
                    $find_document->document_file = $file_new;
                    $find_document->status = 0;
                }
                if($request->get('document_expiry') != Null){
                    $find_document->expiry_date = Carbon::parse($request->get('document_expiry'));
                }
                $find_document->save();
                Session::flash('success', $get_document->name . ' Updated Successfully!');
            } else {
                if ($request->hasFile('document_file')) {
                    $image = $request->file('document_file');
                    $destinationPath = public_path('/assets/images/provider-documents/');

                    $image_extension = $image->extension();
                    $file_new = rand(1, 9) . date('siHYdm') . rand(1, 9) . '.' . $image->getClientOriginalExtension();

                    if ($image_extension != "pdf" && $image_extension != "doc" && $image_extension != "docx") {
                        $img = Image::read($image->getRealPath());
                        $img->orient();
                        $img->resize(500, 500, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPath . $file_new);
                    }else {
                        $image->move($destinationPath, $file_new);
                    }

                    $documents = new ProviderDocuments();
                    $documents->user_id = $driver_details->user_id;
                    $documents->req_document_id = $get_document->id;
                    $documents->document_file = $file_new;
                    if($request->get('document_expiry') != Null){
                        $find_document->expiry_date = Carbon::parse($request->get('document_expiry'));
                    }
                    $documents->status = 0;
                    $documents->save();
                    //$documents->expiry_date = Carbon::parse($request->get('document_expiry'));
                    Session::flash('success', $get_document->name . ' Upload Successfully!');
                }

                if($request->get('document_expiry') != Null){
                    $find_document->expiry_date = Carbon::parse($request->get('document_expiry'));
                }
                $documents->status = 0;
                $documents->save();
                Session::flash('success', $get_document->name . ' Upload Successfully!');
            }
        } else {
            Session::flash('error', 'Document Name Not Found!');
        }
        return redirect()->back()->with('success','Document uploaded successfully !');
    }

    public function postAdminUpdateProviderDocumentExpiry(Request $request)
    {
        $user_id = $request->get('user_id');
        $docs_id = $request->get('docs_id');
        $find_document = ProviderDocuments::where('user_id', $user_id)->where('req_document_id', $docs_id)->first();

        if ($find_document != Null) {
            $find_document->expiry_date = Carbon::parse($request->get('document_expiry'));
            $find_document->save();
            Session::flash('success', 'Expiry date updated successfully!');
        } else {
            $find_document = new ProviderDocuments();
            $find_document->user_id = $user_id;
            $find_document->req_document_id = $docs_id;
            $find_document->expiry_date = Carbon::parse($request->get('document_expiry'));
            $find_document->save();
            Session::flash('success', 'Expiry date saved successfully!');
        }

        return redirect()->back();
    }


    public function getTransportProviderRideReviewList($slug, Request $request)
    {
        $service_category = ServiceCategory::query()->where('slug', $slug)->first();
        $service_cat_id = $service_category->id;
        if (in_array($service_cat_id, [$this->bike_rental, $this->taxi_rental])) {
//            $service_cat_id = $this->bike_ride;
            $provider_reviews = TransportRatings::query()->select('transport_driver_rating.id',
                'transport_driver_rating.rating',
                'transport_driver_rating.comment',
                'transport_driver_rating.status',
                'users.first_name',
                'users.last_name',
                DB::raw("providers.first_name as name"),
                'user_rental_ride_booking.pickup_date_time as pickup_datetime',
                'user_rental_ride_booking.ride_no'
            )
                ->join('user_rental_ride_booking', 'user_rental_ride_booking.id', '=', 'transport_driver_rating.rental_id')
                ->join('users', 'users.id', '=', 'transport_driver_rating.user_id')
                ->join('transport_driver_details', 'transport_driver_details.id', '=', 'transport_driver_rating.driver_id')
                ->join('provider_services', 'provider_services.id', '=', 'transport_driver_details.provider_service_id')
                ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
                ->where('user_rental_ride_booking.service_cat_id', '=', $service_cat_id)
                ->whereNull('users.deleted_at')
                ->whereNull('providers.deleted_at');
            $admin_role = request()->get("admin_role");
            if ($admin_role == 4) {
                $admin_city_id = request()->get("admin_city_id");
                $provider_reviews->where('user_rental_ride_booking.area_id', $admin_city_id);
            }
            $provider_reviews = $provider_reviews->orderBy('transport_driver_rating.id', 'desc')->get();
        } else {
            $provider_reviews = TransportRatings::query()->select(
                'transport_driver_rating.id', 'transport_driver_rating.rating',
                'transport_driver_rating.comment', 'transport_driver_rating.status',
                'users.first_name', 'users.last_name',
                DB::raw("providers.first_name as name"), 'user_ride_booking.pickup_datetime', 'user_ride_booking.ride_no'
            )
                ->join('user_ride_booking', 'user_ride_booking.id', '=', 'transport_driver_rating.ride_id')
                ->join('users', 'users.id', '=', 'transport_driver_rating.user_id')
                ->join('transport_driver_details', 'transport_driver_details.id', '=', 'transport_driver_rating.driver_id')
                ->join('provider_services', 'provider_services.id', '=', 'transport_driver_details.provider_service_id')
                ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
                ->whereNull('providers.deleted_at')
                ->whereNull('users.deleted_at')
                ->where('user_ride_booking.service_cat_id', '=', $service_cat_id);
            $admin_role = request()->get("admin_role");
            if ($admin_role == 4) {
                $admin_city_id = request()->get("admin_city_id");
                $provider_reviews->where('user_ride_booking.area_id', $admin_city_id);
            }
            $provider_reviews = $provider_reviews->orderBy('transport_driver_rating.id', 'desc')->get();
        }
        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.transport_provider_review_list', compact('slug', 'service_category', 'provider_reviews'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.transport_provider_review_list', compact('slug', 'service_category', 'provider_reviews'));
    }

    //get Delete Transport Provider Ride Review
    public function getDeleteTransportProviderRideReview(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');

        if ($id == Null) {
            return response()->json([
                'success' => false
            ]);
        }
        $transport_review = TransportRatings::query()->where('id', '=', $id)->first();
        if ($transport_review == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        $driver_details =TransportDriverDetails::query()->where('user_id', $transport_review->driver_id)->first();
        if ($driver_details == NULL) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found'
            ]);
        }

        $transport_review->delete();
        $ratings = TransportRatings::query()
            ->selectRaw('AVG(rating) as rating')
            ->where('status',1)
            ->where('driver_id', $driver_details->user_id)
            ->first();

        if ($ratings != null){
            $driver_details->rating = $ratings->rating;
        } else {
            $driver_details->rating = 0;
        }

         $driver_details->save();
        return response()->json([
            'success' => true
        ]);
    }

    //get Ajax Transport Provider Ride Review Status Change
    public function getAjaxUpdateProviderRideReviewStatus(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $transport_review = TransportRatings::query()->select('id','driver_id', 'status')->where('id', '=', $id)->first();
        if ($transport_review == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        $driver_details = TransportDriverDetails::query()->where('user_id',$transport_review->driver_id)->first();

        if($driver_details == null){
            return response()->json([
                'success' => false,
                'message' => 'Driver not found'
            ]);
        }
        if ($transport_review->status == 1) {
            $status = $transport_review->status = 0;
        } else {
            $status = $transport_review->status = 1;
        }
        $transport_review->save();
        $ratings = TransportRatings::query()
            ->selectRaw('AVG(rating) as rating')
            ->where('status',1)
            ->where('driver_id', $driver_details->user_id)
            ->first();

        if ($ratings != null){
            $driver_details->rating = $ratings->rating;
        } else {
            $driver_details->rating = 0;
        }
        $driver_details->save();

        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    //get Transport Provider Ride Review List
    public function getTransportProviderReviewList($driver_id, Request $request)
    {

        $driver_details = TransportDriverDetails::query()->select(DB::raw("users.first_name as driver_name"),'users.id as user_id')
            ->join('users', 'users.id', 'transport_driver_details.user_id')
            ->where('transport_driver_details.id', '=', $driver_id)
            ->where('users.is_driver_type', '=', 1)
//            ->where('users.is_driver_status', '=', 1)
            ->whereNull('users.deleted_at')
            ->first();
        if ($driver_details == Null) {
            Session::flash('error', 'Provider not found!');
            return redirect()->back();
        }
        $provider_reviews = TransportRatings::query()->select('transport_driver_rating.id', 'transport_driver_rating.rating',
            'transport_driver_rating.comment', 'transport_driver_rating.status', 'u1.first_name', 'u1.last_name',
            DB::raw("u2.first_name as name"), 'user_ride_booking.pickup_datetime', 'user_ride_booking.ride_no'
        )
            ->join('user_ride_booking', 'user_ride_booking.id', '=', 'transport_driver_rating.ride_id')
            ->join('users as u1', 'u1.id', '=', 'transport_driver_rating.user_id')
            ->join('transport_driver_details', 'transport_driver_details.user_id', '=', 'transport_driver_rating.driver_id')
            ->join('users as u2', 'u2.id', '=', 'transport_driver_details.user_id')
            ->whereNull('u1.deleted_at')
            ->where('transport_driver_rating.driver_id', '=', $driver_details->user_id)
            ->orderBy('transport_driver_rating.id', 'desc')
            ->get();

        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.transport_provider_review_list', compact('driver_details',  'provider_reviews'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.transport_provider_review_list', compact('driver_details',  'provider_reviews'));
    }

    //get Transport Service Category Vehicle Type
    public function getTransportServiceCategoryVehicleType(Request $request)
    {
        $vehicle_type_lists = TransportVehicleType::query()->select(
            'transport_vehicle_type.id','transport_vehicle_type.service_id', 'transport_vehicle_type.name',
            'transport_vehicle_type.icon_name', 'transport_vehicle_type.cost_for_km', 'transport_vehicle_type.status','vehicle_services.name as service_name')
            ->join('vehicle_services','vehicle_services.id','=','transport_vehicle_type.service_id')
            ->orderBy('transport_vehicle_type.id', 'desc')
            ->get();
        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.vehicle_type.manage_vehicle_type', compact( 'vehicle_type_lists'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.vehicle_type.manage_vehicle_type', compact( 'vehicle_type_lists'));
    }


    //get Add Transport Vehicle Type
    public function getAddTransportVehicleType(Request $request)
    {
        $vehicle_services = VehicleService::query()->where('status',1)->get();
        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.vehicle_type.form_vehicle_type',compact('vehicle_services'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.vehicle_type.form_vehicle_type',compact('vehicle_services'));
    }

    //get Edit Transport Vehicle Type
    public function getEditTransportVehicleType($id, Request $request)
    {
        $transport_vehicle_type = TransportVehicleType::query()->where('id', $id)->first();
        $vehicle_services = VehicleService::query()->where('status',1)->get();
        $check_vehicle_type = TransportDriverDetails::query()
            ->where('vehicle_type_id', $transport_vehicle_type->id)
            ->first();

        if ($transport_vehicle_type != Null) {
            $view = view('admin.pages.transport_services.vehicle_type.form_vehicle_type', compact('transport_vehicle_type', 'vehicle_services','check_vehicle_type'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        } else {
            Session::flash('error', 'Vehicle Type Not Found!');
            return redirect()->back();
        }
    }

    //Update or Store Transport Vehicle Type
    public function postUpdateTransportVehicleType(TransportVehicleTypeRequest $request) {
        if($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $id = $request->get('id');
        if ($id != Null) {
            $vehicle_type = TransportVehicleType::query()->where('id', $id)->first();
            Session::flash('success', 'Vehicle Type Updated successfully!');
        } else {
            $vehicle_type = new TransportVehicleType();
            Session::flash('success', 'Vehicle Type Added successfully!');
        }
            $vehicle_type->name = $request->get('name');
        if ($request->file('icon')) {
            if (\File::exists(public_path('/assets/images/service-category/transport-service-type/' . $vehicle_type->icon_name))) {
                \File::delete(public_path('/assets/images/service-category/transport-service-type/' . $vehicle_type->icon_name));
            }
            $file = $request->file('icon');
            $file_new = $request->get('service_cat_id') . date('siHYdm') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/service-category/transport-service-type/', $file_new);
            $vehicle_type->icon_name = $file_new;
        }
        $vehicle_type->service_id = $request->get('service_id');
//        $vehicle_type->cost_for_km = $request->get('cost_for_km');
        $vehicle_type->status = $request->get('status');
        $vehicle_type->save();
        return redirect()->route('get:admin:vehicle_type');
    }

    //get Delete Transport Vehicle Type
    public function getDeleteTransportVehicleType(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false
            ]);
        }

        $vehicle_type = TransportVehicleType::query()->where('id', '=', $id)->first();
        if ($vehicle_type == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        $check = TransportDriverDetails::query()->where('vehicle_type_id', $vehicle_type->id)->first();
        if ($check != Null) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry ! cannot deleted this vehicle type as many of the drivers are registered with this !'
            ]);
        }
        if (\File::exists(public_path('/assets/images/service-category/transport-service-type/' . $vehicle_type->icon_name))) {
            \File::delete(public_path('/assets/images/service-category/transport-service-type/' . $vehicle_type->icon_name));
        }
        $vehicle_type->delete();
        return response()->json([
            'success' => true
        ]);
    }

    //get Ajax Transport Vehicle Type Status Change
    public function getAjaxUpdateTransportVehicleTypeStatus(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $vehicle_type_details = TransportVehicleType::query()->select('id', 'status')->where('id', '=', $id)->first();
        if ($vehicle_type_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($vehicle_type_details->status == 1) {

            $check = TransportDriverDetails::query()
                ->where('vehicle_type_id', $vehicle_type_details->id)
                ->first();

            if ($check != Null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry ! cannot Disable this vehicle type as many of the drivers are registered with this !'
                ]);
            }

            $status = $vehicle_type_details->status = 0;
        } else {
            $status = $vehicle_type_details->status = 1;
        }
        $vehicle_type_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }


    //get Edit Transport Vehicle Type
    public function getEditTransportProviderVehicleDetails($id, Request $request)
    {
        $transport_vehicle_type = TransportVehicleType::query()->get();
        $transport_provider = TransportDriverDetails::query()->where('id', $id)->first();
        $view = view('admin.pages.transport_services.driver_vehicle_type_details.form', compact('transport_vehicle_type',  'transport_provider'));
        if ($transport_provider != Null) {
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
//            return view('admin.pages.transport_services.driver_vehicle_type_details.form', compact('slug', 'transport_vehicle_type', 'service_category', 'transport_provider'));
        } else {
            Session::flash('error', 'Driver Vehicle Details Not Found!');
            return redirect()->back();
        }
    }

    //Update or Store Transport Vehicle Type
    public function postUpdateTransportProviderVehicleDetails(Request $request)
    {
        if($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $validator = $request->validate([
            'model_year' => 'required',
        ]);
        $id = $request->get('id');
        if (!is_numeric($id) || $id == Null) {
            Session::flash('error', 'Driver Vehicle Details Not Updated!');
            return redirect()->back();
        }
        $transport_driver_details = TransportDriverDetails::query()->where('id', $id)->first();
        if ($transport_driver_details == Null) {
            Session::flash('error', 'Driver Vehicle Details Not Updated!');
            return redirect()->back();
        }
        $transport_driver_details->vehicle_type_id = $request->get('vehicle_type_id');
        $transport_driver_details->vehicle_company = $request->get('vehicle_company');
        $transport_driver_details->model_name = $request->get('model_name');
        $transport_driver_details->plat_no = $request->get('plat_no');
        $transport_driver_details->model_year = $request->get('model_year');
        $transport_driver_details->vehicle_color = $request->get('vehicle_color');
        $transport_driver_details->save();

        $provider_list_check = TransportDriverDetails::query()->select('users.is_driver_status as status')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->where('transport_driver_details.id', '=', $id)
            ->first();
        $status = "approved";
        if ($provider_list_check != Null) {
            if ($provider_list_check->status == 1) {
                $status = "approved";
            } elseif ($provider_list_check->status == 0) {
                $status = "un-approved";
            } elseif ($provider_list_check->status == 2) {
                $status = "blocked";
            } elseif ($provider_list_check->status == 3) {
                $status = "rejected";
            }
        }
        Session::flash('success', 'Driver Vehicle Details Updated successfully!');
        return redirect()->route('get:admin:transport_service_provider_list', $status);
    }

    //get Transport Service Category Site Setting
    public function getTransportServiceSetting(Request $request)
    {
        $service_settings = ServiceSettings::query()->first();
        $view = view('admin.pages.transport_services.site_setting.form', compact( 'service_settings'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //Save or Update Sransport Service Setting
    public function postUpdateTransportServiceSetting(Request $request)
    {
        if($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $service_settings = $this->adminClass->ServiceSettingsStore($request);
        $id = $request->get('id');
        if ($id != Null) {
            Session::flash('success', 'Service Settings Updated successfully!');
        } else {
            Session::flash('success', 'Service Settings Added successfully!');
        }
            return redirect()->route('get:admin:service_setting');

    }

    public function getVehicleCommissionRates(Request $request)
    {
        (new \Database\Seeders\VehicleCommissionRateSeeder())->run();
        $rates = \App\Models\VehicleCommissionRate::query()->orderBy('sort_order')->get();
        $service = ServiceSettings::query()->first();
        $general = \App\Models\GeneralSettings::query()->first();
        $view = view('admin.pages.transport_services.vehicle_commission.manage', [
            'rates' => $rates,
            'global_commission' => (float) ($service->admin_commission ?? config('xisti.default_commission_percent', 8)),
            'vat_rate' => (float) ($general->vat_rate_on_commission ?? 19),
        ]);
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postUpdateVehicleCommissionRates(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.id' => 'required|integer|exists:vehicle_commission_rates,id',
            'rates.*.commission_percent' => 'required|numeric|min:0|max:100',
            'rates.*.status' => 'required|in:0,1',
        ]);

        foreach ($validated['rates'] as $row) {
            \App\Models\VehicleCommissionRate::query()
                ->where('id', $row['id'])
                ->update([
                    'commission_percent' => round((float) $row['commission_percent'], 2),
                    'status' => (int) $row['status'],
                ]);
        }

        Session::flash('success', 'Comisiones por vehículo actualizadas correctamente.');
        return redirect()->route('get:admin:vehicle_commission_rates');
    }


    public function getTransportEarningReport(Request $request) {
        if (Auth::guard("admin")->check()) {
            if (Auth::guard("admin")->user()->roles == 1 || Auth::guard("admin")->user()->roles == 4) {
            } elseif (Auth::guard("admin")->user()->roles == 2) {
                return redirect()->route('get:dispatcher:manual_ride_booking');
            } elseif (Auth::guard("admin")->user()->roles == 3) {
                return redirect()->route('get:account:dashboard');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('get:admin:login');
            }
        } else {
            Auth::guard('admin')->logout();
            return redirect()->route('get:admin:login');
        }

        $user_list = TransportRideBook::query()->select('users.id', 'users.first_name', 'users.last_name', 'users.contact_number')
            ->join('users', 'users.id', '=', 'user_ride_booking.user_id')
            ->where('user_ride_booking.status', '=', 9);
        $admin_role = request()->get("admin_role");
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $user_list->where('user_ride_booking.area_id', $admin_city_id);
        }
        $user_list = $user_list->groupBy('user_ride_booking.user_id')->get();

        $driver_list = TransportRideBook::query()->select('transport_driver_details.id', DB::raw("users.first_name as name"), 'users.contact_number', 'user_ride_booking.driver_id')
            ->join('transport_driver_details', 'transport_driver_details.user_id', '=', 'user_ride_booking.driver_id')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->where('user_ride_booking.status', '=', 9);
        $driver_list = $driver_list->groupBy('user_ride_booking.driver_id')->get();

        $from_date = Null;
        $to_date = Null;
        $driver = Null;
        $user = Null;
        $payment_type = Null;
        $driver_pay_type = Null;

        $view = view('admin.pages.transport_services.transport_earings_reports', compact('from_date', 'to_date', 'driver', 'user', 'payment_type', 'driver_pay_type', 'user_list', 'driver_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //post transport search earning report
    public function PostTransportSearchEarningReport(Request $request)
    {
        if (Auth::guard("admin")->check()) {
            if (Auth::guard("admin")->user()->roles == 1 || Auth::guard("admin")->user()->roles == 4) {

            }else {
                Auth::guard('admin')->logout();
                return redirect()->route('get:admin:login');
            }
        } else {
            Auth::guard('admin')->logout();
            return redirect()->route('get:admin:login');
        }
        $user_list = TransportRideBook::query()->select('users.id', 'users.first_name', 'users.last_name', 'users.contact_number')
            ->join('users', 'users.id', '=', 'user_ride_booking.user_id')
            ->where('user_ride_booking.status', '=', 9)
            ->whereNull('users.deleted_at');
        $admin_role = request()->get("admin_role");
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $user_list->where('users.area_id', $admin_city_id);
        }
        $user_list = $user_list->groupBy('user_ride_booking.user_id')->get();

        $driver_list = TransportRideBook::query()->select('transport_driver_details.id', DB::raw("users.first_name as name"), 'users.contact_number', 'user_ride_booking.driver_id')
            ->join('transport_driver_details', 'transport_driver_details.user_id', '=', 'user_ride_booking.driver_id')
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->where('user_ride_booking.status', '=', 9)
            ->whereNull('users.deleted_at');
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $driver_list->where('users.area_id', $admin_city_id);
        }
        $driver_list = $driver_list->groupBy('user_ride_booking.driver_id')->get();

        $from_date = Null;
        $to_date = Null;
        $driver = Null;
        $user = Null;
        $payment_type = Null;
        $driver_pay_type = Null;
        $admin_report_details = TransportRideBook::query()->select(
            'user_ride_booking.id',
            'user_ride_booking.ride_no',
            'user_ride_booking.driver_pay_settle_status',
            'user_ride_booking.offered_price',
            'user_ride_booking.pickup_datetime',
            'user_ride_booking.ride_type',
            'user_ride_booking.user_name', 'user_ride_booking.driver_name',
            'user_ride_booking.total_distance_amount',
            'user_ride_booking.tip',
            'user_ride_booking.refer_discount',
            'user_ride_booking.status',
            'user_ride_booking.payment_type',
            'user_ride_booking.total_pay',
            'user_ride_booking.driver_amount',
            'user_ride_booking.admin_commission',
            'user_ride_booking.toll_charge',
            'user_ride_booking.ride_for_other',
            'user_ride_booking.is_hail',
            'user_ride_booking.other_user_name',
        )
            ->where('user_ride_booking.status', '=', 9);

        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $admin_report_details->where('user_ride_booking.area_id', $admin_city_id);
        }
        if ($request->get('from_date') != Null && $request->get('to_date') != Null) {
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            $from = Date('Y-m-d', strtotime($from_date)) . " 00:00:00";
            $to = Date('Y-m-d', strtotime($to_date)) . " 23:59:59";
            $admin_report_details->whereDate('user_ride_booking.pickup_datetime', '>=', $from);
            $admin_report_details->whereDate('user_ride_booking.pickup_datetime', '<=', $to);
        }

        if ($request->get('driver') != Null) {
            $driver = $request->get('driver');
            $admin_report_details->where('user_ride_booking.driver_id', '=', $request->get('driver'));
        }
        if ($request->get('user') != Null) {
            $user = $request->get('user');
            $admin_report_details->where('user_ride_booking.user_id', '=', $request->get('user'));
        }
        if ($request->get('payment_type') != Null) {
            //cash & card
            $payment_type = $request->get('payment_type');
//            if ($payment_type != 2) {
            $admin_report_details->where('user_ride_booking.payment_type', '=', $request->get('payment_type'));
//            }
        }
        if ($request->get('driver_pay_type') != Null) {
            $driver_pay_type = $request->get('driver_pay_type');
            if ($driver_pay_type != 2) {
                $admin_report_details->where('user_ride_booking.driver_pay_settle_status', '=', $request->get('driver_pay_type'));
            }
        }
        $payment_reports = $admin_report_details->orderBy("user_ride_booking.id", "desc")->get();

        if (!$payment_reports->isEmpty()) {
            $total_ride_check = 0;
            $site_earning_check = 0;
            $driver_earning = 0;
            $total_driver_pay_earning = [];
            $total_driver_pay_amount = 0;
            $total_collect_payment_check = 0;
            $collect_payment_total = [];
            foreach ($payment_reports as $key => $report) {
                $total_ride_earning[$report->ride_no] = $report->total_pay;
                $total_ride_check = array_sum($total_ride_earning);

                $site_earning[$report->ride_no] = $report->admin_commission;
                $site_earning_check = array_sum($site_earning);

                $driver_commission[$report->ride_no] = $report->driver_amount;
                $driver_earning = array_sum($driver_commission);

                if ($report->payment_type == 2 || $report->payment_type == 3) {
                    $total_driver_pay_earning[$report->ride_no] = $report->driver_amount;
                    $total_driver_pay_amount = array_sum($total_driver_pay_earning);
                }

                $collect_payment[$report->ride_no] = round(($report->total_pay - ($driver_commission[$report->ride_no])),2);
                if ($report->payment_type == 1) {
                    $collect_payment_total[$report->ride_no] = ($report->total_pay - ($driver_commission[$report->ride_no]));
                    $total_collect_payment_check = array_sum($collect_payment_total);
                }
            }
            $total_ride = number_format($total_ride_check, 2);
            $total_site_earning = number_format($site_earning_check, 2);
            $total_driver_earning = number_format($driver_earning, 2);
//            $total_discount = number_format($total_used_promocode, 2);
            $total_discount = number_format($admin_report_details->sum("refer_discount"),2);
            $total_pay_driver = number_format($total_driver_pay_amount, 2);
            $total_collect_payment = number_format($total_collect_payment_check, 2);
            $total_outstanding = $total_driver_pay_amount - $total_collect_payment_check;
            $total_driver_outstanding_amount = number_format($total_outstanding, 2);

            $view = view('admin.pages.transport_services.transport_earings_reports', compact( 'from_date', 'to_date', 'driver', 'user', 'payment_type', 'driver_pay_type', 'user_list', 'driver_list',
                'payment_reports','driver_commission', 'collect_payment','total_ride', 'total_site_earning', 'total_driver_earning', 'total_discount', 'total_pay_driver', 'total_collect_payment', 'total_driver_outstanding_amount'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        } else {
            $view = view('admin.pages.transport_services.transport_earings_reports', compact( 'from_date', 'to_date', 'driver', 'user', 'payment_type', 'driver_pay_type', 'user_list', 'driver_list'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        }
    }


    //post transport driver payment settled
    public function postTransportDriverPaymentSettled(Request $request)
    {
        $slug="taxi-ride";
        if($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        if (Auth::guard("admin")->check()) {
            if (Auth::guard("admin")->user()->roles != 1) {
                return response()->json([
                    "status" => false,
                    "message" => 'Something went to wrong!'
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => 'Something went to wrong!'
            ]);
        }
        $driver_pay_details = $request->get('ride_id');
        if ($driver_pay_details != Null) {
            foreach ($driver_pay_details as $key => $pay) {
                $pay_store = TransportRideBook::query()->where('id', $pay)->first();
                if ($pay_store != Null && $pay_store->driver_pay_settle_status == 0) {
                    $pay_store->driver_pay_settle_status = 1;
                    $pay_store->save();
                }
            }
        }
        return response()->json([
            "success" => true,
            "message" => "success"
        ]);
//        return redirect()->route('get:admin:transport_earning_report', $slug);

    }


    //provider display on map in My Bantay function
    public function getAdminTransportProviderLocation(Request $request) {

        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::query()->select('status')->where('id', $running->booking_id)->first();
        }
        $providers_detials = TransportDriverDetails::query()->select(
            'transport_driver_details.id',
            'users.id as provider_id', DB::raw("users.first_name as name"), 'users.contact_number','users.country_code',
            'users.avatar',
            'transport_driver_details.current_lat',
            'transport_driver_details.current_long','transport_driver_details.plat_no',
            'transport_driver_details.availability_ride_status'
        )
            ->join('users', 'users.id', 'transport_driver_details.user_id')
            ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
            ->where('users.status', 1)
            ->where('users.driver_current_status', 1)
            ->whereNotNull('users.access_token')
            ->whereNull('users.deleted_at');

        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $providers_detials->where('users.area_id', request()->get("admin_city_id"));
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/
        $providers_detials = $providers_detials->get();
        $availability_ride_status = 3;
        if ($providers_detials->isEmpty()) {
            $providers = [];
        } else {
            foreach ($providers_detials as $key => $provider) {
                $availability_ride_status = 3;
                if (array_key_exists($provider->provider_id, $transport_running_service)) {
                    if (in_array($transport_running_service[$provider->provider_id]['status'], $this->available)) {
                        $availability_ride_status = 3;
                    } elseif (in_array($transport_running_service[$provider->provider_id]['status'], $this->ride_start)) {
                        $availability_ride_status = 2;
                    } elseif (in_array($transport_running_service[$provider->provider_id]['status'], $this->reached)) {
                        $availability_ride_status = 1;
                    } elseif (in_array($transport_running_service[$provider->provider_id]['status'], $this->enroute)) {
                        $availability_ride_status = 0;
                    }
                }
                $providers[$key] = [
                    "id" => $provider->id,
                    "name" => $provider->name,
                    "last_name" => Null,
                    "image" => $provider->avatar,
                    "latitude" => $provider->current_lat,
                    "longitude" => $provider->current_long,
                    "address" => "",
                    "ride_status" => $availability_ride_status,
                    "phone" =>$provider->country_code."".$provider->contact_number,
                    "plat_no" =>$provider->plat_no,
                ];
            }
        }
        $view = view('admin.pages.transport_services.transport_gods_view', compact( 'providers'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminTransportAllProviderLocation(Request $request)
    {
        $check_type = 0;
        return $this->adminClass->AllProviderLocation($check_type);
    }

    public function getAdminTransportAvailableProviderLocation(Request $request)
    {

        $check_type = 0;
        return $this->adminClass->AvailableProviderLocation($check_type);
    }

    public function getAdminTransportRideStartProviderLocation(Request $request)
    {
        $check_type = 0;
        return $this->adminClass->RideStartProviderLocation($check_type);
    }

    public function getAdminTransportRideReachedProviderLocation(Request $request)
    {
        $check_type = 0;
        return $this->adminClass->RideReachedProviderLocation($check_type);

    }

    public function getAdminTransportRideEnrouteProviderLocation(Request $request)
    {
        $check_type = 0;
        return $this->adminClass->RideEnrouteProviderLocation($check_type);
    }

    //using ajax provider display on map in My Bantay function
    public function getAdminTransportLocationOnMap(Request $request)
    {
        $current_status = $request->get('current_status');
        if ($current_status == Null) {
            return Null;
        }
        $check_type = 0;
        if ($current_status != "all") {
            if ($current_status == "available") {
                return $this->adminClass->AvailableProviderLocation($check_type);
            } elseif ($current_status == "ride_start") {
                return $this->adminClass->RideStartProviderLocation($check_type);
            } elseif ($current_status == "reached_pickup") {
                return $this->adminClass->RideReachedProviderLocation($check_type);
            } elseif ($current_status == "enroute_pickup") {
                return $this->adminClass->RideEnrouteProviderLocation($check_type);
            } else {
                return $this->adminClass->AllProviderLocation($check_type);
            }
        } else {
            return $this->adminClass->AllProviderLocation($check_type);
        }
    }

    //using ajax provider display on map in My Bantay function
    public function getAdminTransportSearchProviderOnMap(Request $request)
    {
        $search_provider_name = $request->get('provider');
        $current_status = $request->get('driver_current_status');
        if ($current_status == Null) {
            return $providers = [];
        }
        $check_type = 0;
        if ($current_status != "all") {
            if ($current_status == "available") {
                return $this->adminClass->AvailableProviderLocation($check_type, $search_provider_name);
            } elseif ($current_status == "ride_start") {
                return $this->adminClass->RideStartProviderLocation($check_type, $search_provider_name);
            } elseif ($current_status == "reached_pickup") {
                return $this->adminClass->RideReachedProviderLocation($check_type, $search_provider_name);
            } elseif ($current_status == "enroute_pickup") {
                return $this->adminClass->RideEnrouteProviderLocation($check_type, $search_provider_name);
            } else {
                return $this->adminClass->AllProviderLocation($check_type, $search_provider_name);
            }
        } else {
            return $this->adminClass->AllProviderLocation($check_type, $search_provider_name);
        }
    }


    public function getTransportUpdateRideStatus(Request $request)
    {
        info($request->all());
//        dd($request->all());
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric",
            "update_status" => "required|numeric|in:4,9"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $id = $request->get('id');
        $update_status = $request->get('update_status');
        if ($update_status == 4) {
            $validator = Validator::make($request->all(), [
                "reason" => "required"
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => $validator->errors()->first()
                ]);
            }
        }
        $transport_ride = TransportRideBook::query()->where('id', $id)->first();
        if ($transport_ride == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($transport_ride->status == 4) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user_details = User::query()->where('id', $transport_ride->user_id)->first();
        switch ($update_status) {
            case 4:
                if ($transport_ride->status != 4 && $transport_ride->status != 9 && $transport_ride->status != 10) {
                    $ride_status = $transport_ride->status;
                    $transport_ride->otp = Null;
                    $transport_ride->status = $update_status;
                    $transport_ride->cancel_by = "admin";
                    $transport_ride->cancel_reason = $request->get('reason');
                    $transport_ride->save();

                    //deleting chat from firebase
                    (new FirebaseService())->deleteOrderChat($transport_ride->ride_no,$transport_ride->id);

                    UserRunningRide::query()->where('user_id', $transport_ride->user_id)->where('booking_id', $transport_ride->id)->delete();

                    //refer history code
                    if($ride_status >= 0 && $ride_status <= 9 ){
                        if ($transport_ride->refer_discount > 0) {
                            $user = User::query()->select('id','pending_refer_discount')->where('id', $transport_ride->user_id)->whereNull('deleted_at')->first();
                            if ($user != Null) {
                                $user_refer_history = UserReferHistory::query()->where('id',$transport_ride->user_refer_history_id)->where('user_id', $transport_ride->user_id)->where('user_status', 1)->first();
                                if ($user_refer_history != Null) {
                                    $user_refer_history->user_status = 0;
                                    $user_refer_history->save();
                                    $user->pending_refer_discount = $user->pending_refer_discount + 1;
                                    $user->save();
                                } else {
                                    $user_refer_history = UserReferHistory::query()->where('id',$transport_ride->user_refer_history_id)->where('refer_id', $transport_ride->user_id)->where('refer_status', 1)->first();
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
                    if ($user_details != Null) {
                        $this->notificationClass->userTransportNotification($transport_ride->id, $user_details->device_token, $transport_ride->status, $user_details->login_device,$user_details->language);
                    }
                    if ($transport_ride->driver_id != Null) {
                        ProviderUserRunningService::query()->where('booking_id', $transport_ride->id)->delete();
                        $driver_details = User::query()->select('id','device_token','login_device','language')
                            ->where('id', $transport_ride->driver_id)
                            ->whereNull('users.deleted_at')
                            ->first();
                        if ($driver_details != Null) {
                            $cancel_by = "admin";
                            $this->notificationClass->driverCancelNotification($transport_ride->id,$driver_details->device_token,$driver_details->id, $driver_details->login_device,$driver_details->language,$cancel_by);
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data not found'
                    ]);
                }
                break;
            case 9:
                if ($transport_ride->status > 0) {
                    if ($transport_ride->status != 4 && $transport_ride->status != 9 && $transport_ride->status != 10) {
                        $update_driver_status = Null;
                        $driver_details = Null;
                        if ($transport_ride->driver_id != Null) {
                            $driver_details = User::query()->select('id','device_token','login_device','language')
                                ->where('id', $transport_ride->driver_id)
                                ->whereNull('users.deleted_at')
                                ->first();
                            if ($driver_details == Null) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Data not found'
                                ]);
                            }
                        }
                        $general_settings = request()->get("general_settings");
                        //code for applying toll charge
                        //code for dynamic Toll charge module 0 - off , 1 - driver will give the final charge , 2 - driver will give no of tolls & charge per toll is decided by admin
                        if($general_settings->is_toll_module > 0){
                            $no_of_toll = 0;
                            if($general_settings->is_toll_module == 1){
                                $toll_charge = ($request->get('toll_charge') != null ) ? $request->get('toll_charge') : 0;//final toll charge
                            }else if($general_settings->is_toll_module == 2){
                                //if 2 - driver will give no of tolls & charge per toll is decided by admin
                                $validator = Validator::make($request->all(), [
                                    "no_of_toll" => "required|numeric",
                                ]);
                                if ($validator->fails()) {
                                    return response()->json([
                                        "status" => false,
                                        "message" => $validator->errors()->first()
                                    ]);
                                }
                                $no_of_toll = $request->get('no_of_toll');//no of tolls
                                $admin_charge_per_toll = $general_settings->charge_per_toll ? $general_settings->charge_per_toll : 0;//charge per toll set by admin
                                $toll_charge = round($no_of_toll * $admin_charge_per_toll,2);//final toll charge
                            }
                            //updating toll charge
                            $transport_ride->no_of_toll = $no_of_toll;
                            $transport_ride->toll_charge = $toll_charge;
                            $transport_ride->total_pay = round($transport_ride->total_pay + $toll_charge,2);
                            $transport_ride->driver_amount = round($transport_ride->driver_amount + $toll_charge, 2);
                            $transport_ride->save();
                        }
                        //code for auto settle payment module
                        $driver_data = User::query()->select('id','device_token','login_device','language')
                            ->where('id', $transport_ride->driver_id)
                            ->whereNull('users.deleted_at')
                            ->first();
                        $transport_ride->payment_type = $transport_ride->payment_status = 1;
                        if ($general_settings != Null) {
                            if ($general_settings->auto_settle_wallet == 1 && $transport_ride->driver_pay_settle_status != 1) {
                                $ride_no = $transport_ride->ride_no;
                                $transaction_type = 0;
                                $subject = '';
                                $subject_code = 0;
                                if($transport_ride->admin_commission > 0){
                                    //if payment type is cash
                                    if ($transport_ride->payment_type == 1) {
                                        $provider_id = $driver_data->id;
                                        $wallet_provider_type = 0;
                                        $transaction_type = 2;
                                        $add_update_wallet_bal = $transport_ride->admin_commission;
                                        $subject = "Admin Debited commission - #" . $ride_no;
                                        $subject_code = 16;
                                    } elseif ($transport_ride->payment_type == 2 || $transport_ride->payment_type == 3) {
                                        //if payment type is not cash
                                        $transaction_type = 1;
                                        $subject = "Credited by Admin for your earning - " ." Booking # " . $transport_ride->ride_no;
                                        $subject_code = 15;
                                        $provider_id = $driver_data->id;
                                        $wallet_provider_type = 0;
                                        $add_update_wallet_bal = $transport_ride->driver_amount;
                                    }
                                    $driver_wallet_update = $this->notificationClass->providerUpdateWalletBalance($provider_id, $wallet_provider_type, $transaction_type, $add_update_wallet_bal, $subject, $subject_code, $ride_no);

                                    if ($driver_wallet_update) {
                                        $transport_ride->driver_pay_settle_status = 1;
                                    }
                                }
                            }
                        }
                        //code for auto settle payment module
                        $transport_ride->status = 9;
                        $transport_ride->completed_by = 1;
                        $transport_ride->otp = Null;
                        $transport_ride->save();

                        //deleting chat from firebase
                        (new FirebaseService())->deleteOrderChat($transport_ride->ride_no,$transport_ride->id);

                        if ($user_details != Null) {
                            $this->notificationClass->userTransportNotification($transport_ride->id, $user_details->device_token, $transport_ride->status, $user_details->login_device,$user_details->language);
                        }
                        UserRunningRide::query()->where('user_id', $transport_ride->user_id)->where('booking_id', $transport_ride->id)->delete();
                        if ($update_driver_status != Null) {
                            $update_driver_status->current_status = 1;
                            $update_driver_status->save();
                        }
                        //$service_category = ServiceCategory::query()->where('id', $transport_ride->service_cat_id)->first();
                        if ($driver_details != Null){
                            ProviderUserRunningService::query()->where('provider_id', $driver_details->id)->where('booking_id', $transport_ride->id)->delete();
                            $this->notificationClass->driverCompletedNotification($driver_details->device_token,$driver_details->login_device,$driver_details->language,$transport_ride->id,$transport_ride->status);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data not found'
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data not found'
                    ]);
                }
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
        }
        return response()->json([
            'success' => true,
            'toll_charge'=>$transport_ride->toll_charge,
            'total_pay'=>$transport_ride->total_pay,
        ]);
    }


    public function getTransportSingleProviderRideList(Request $request){
        $status_check = $request->get('status_check');
        $slug = $request->get('slug');
        $driver_id = $request->get('driver_id');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $rowperpage = (isset($rowperpage) && $rowperpage > 0)?$rowperpage:25;
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $ride_lists = TransportRideBook::query()->select('user_ride_booking.id', 'user_ride_booking.ride_no', 'user_ride_booking.total_pay', 'user_ride_booking.status', 'user_ride_booking.pickup_address', 'user_ride_booking.destination_address', 'user_ride_booking.user_name', 'user_ride_booking.driver_name','user_ride_booking.ride_for_other','user_ride_booking.is_hail','user_ride_booking.other_user_name', 'transport_vehicle_type.name as vehicle_name')
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'user_ride_booking.vehicle_type_id')
            ->where('user_ride_booking.driver_id', '=', $driver_id);
        if ($columnName != 'no'){
            $ride_lists = $ride_lists->orderBy($columnName, $columnSortOrder);
        }else{
            $ride_lists = $ride_lists->orderBy('user_ride_booking.id', 'desc');
        }

        $ride_list_data = $ride_lists;
        $totalRecords = $ride_lists->count();
        if($searchValue != ""){
            $totalRecordswithFilter = $ride_lists->where(function($query) use ($searchValue){
                $query->orWhere('user_ride_booking.ride_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('user_ride_booking.user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.driver_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.total_pay', 'like', '%' .$searchValue . '%');
            })->count();
        } else{
            $totalRecordswithFilter = $ride_lists->count();
        }

        $record = $ride_list_data;
        if($searchValue != ""){
            $record = $record->where(function($query) use ($searchValue){
                $query->orWhere('user_ride_booking.ride_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('user_ride_booking.user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.driver_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('user_ride_booking.total_pay', 'like', '%' .$searchValue . '%');
            });
        }
        $records = $record->skip($start)->take($rowperpage)->get();

        $i = 1;
        $data_arr = [];
        foreach ($records as $record){
            $ride_details_html = "";
            $ride_status = "";
            if($record->status == 0){
                $ride_status = "pending";
            } else if($record->status == 1 || $record->status == 2 || $record->status == 3){
                $ride_status = "approved";
            } else if($record->status == 4){
                $ride_status = "cancelled";
            } else if($record->status == 5 || $record->status == 6 || $record->status == 7 || $record->status == 8){
                $ride_status = "running";
            } else if($record->status == 9){
                $ride_status = "completed";
            } else if($record->status == 10){
                $ride_status = "failed";
            }
            $ride_status_html = '<span class="'.$ride_status.'">'.$ride_status.'</span>';
            $transport_services = $this->transport_service_id_array;
            $courier_service = $this->courier_service_id_array;
            if(Auth::guard("admin")->check()){
                if(Auth::guard("admin")->user()->roles == 1 || Auth::guard("admin")->user()->roles == 4){
                        $ride_details_html = '<a href="'.route('get:admin:ride_details',[$record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';

                } elseif(Auth::guard("admin")->user()->roles == 2){
                    if(in_array($record->service_cat_id, $transport_services)){
                        $ride_details_html = '<a href="'.route('get:dispatcher:ride_details',[ $slug, $record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';
                    }
                } elseif(Auth::guard("admin")->user()->roles == 3){
                    if(in_array($record->service_cat_id, $transport_services)){
                        $ride_details_html = '<a href="'.route('get:account:ride_details',[$record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Ride Details"></i>
                                                </span>
                                               </a>';
                    } elseif(in_array($record->service_cat_id, $courier_service)){
                        $ride_details_html = '<a href="'.route('get:account:ride_details',[$record->id]).'" class="render_link">
                                                <span class="icon-list-demo">
                                                    <i class="fa fa-info-circle text-c-green" data-toggle="tooltip" data-placement="top" title="Delivery Details"></i>
                                                </span>
                                               </a>';
                    }
                }
            }
            $address = '<i class="fa fa-map-marker text-success"></i> '.$record->pickup_address.'<br> <i class="fa fa-map-marker text-danger"></i> '. $record->destination_address;
            $data_arr[] = array(
                "no" => $i,
                "ride_no" => $record->ride_no,
                "user_name" => ($record->ride_for_other == 1 || $record->is_hail == 1) ? $record->other_user_name : $record->user_name,
                "driver_name" => $record->driver_name,
                "vehicle_name" => $record->vehicle_name,
                "total_pay" => '<span class="currency"></span>'.$record->total_pay,
                'status' =>$ride_status_html,
                'details'=>$ride_details_html,
                'address'=>$address
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        return json_encode($response);

    }


    public function getTransportProvidersApprovedList(Request $request) {
        $status = "approved";
        return $this->getTransportProvidersList($request, $status);
    }

    public function getTransportProvidersUnApprovedList(Request $request){
        $status = "un-approved";
        return $this->getTransportProvidersList($request, $status);
    }

    public function getTransportProvidersBlockedList(Request $request){
        $status = "blocked";
        return $this->getTransportProvidersList($request, $status);
    }

    public function getTransportProvidersRejectedList(Request $request){
        $status = "rejected";
        return $this->getTransportProvidersList($request, $status);
    }

    public function getTransportProvidersList($request, $status) {
        if ($status == "approved") {
            $status = 1;
        }
        elseif ($status == "un-approved") {
            $status = 0;
        }
        elseif ($status == "blocked") {
            $status = 2;
        }
        elseif ($status == "rejected") {
            $status = 3;
        }
        else {
            Session::flash('error', 'Category provider list not found!');
            return redirect()->back();
        }

        $provider_lists = null;
        $service_category_id = 0;
        $view = view('admin.pages.transport_services.transport_provider_list', compact('status','provider_lists','service_category_id'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAddTransportDriver(Request $request) {

        $vehicle_services = VehicleService::query()->where('status','=',1)->get();
        // if vehicle service enable then only vehicle type fetch
        if(!$vehicle_services->isEmpty()){
            $vehicle_services_ids = $vehicle_services->pluck('id');
            $vehicle_types = TransportVehicleType::query()->whereIn('service_id',$vehicle_services_ids)->where('status', '=', 1)->get();
        }

        if (isset($vehicle_types) && !$vehicle_types->isEmpty()) {
            $view = view('admin.pages.transport_services.providers.driver_form', compact('vehicle_types','vehicle_services'));
        } else {
            $view = view('admin.pages.transport_services.providers.driver_form');
        }
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getTransportVehicleTypeList(Request $request)
    {
        $serviceId = $request->query('service_id');
//        $service_cat_id = $request->get('service_cat_id');
//        if ($service_cat_id == Null) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Data not found'
//            ]);
//        }
        $get_vehicle_type_list = TransportVehicleType::where('service_id', $serviceId)->where('status', '=', 1)->get();

//        $service_category = ServiceCategory::query()->where('id', $service_cat_id)->first();
//        if ($service_category == Null) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Service category not found'
//            ]);
//        }
        $vehicle_type_list[] = "<option disabled selected>Select Vehicle Type</option>";
//
        if ($get_vehicle_type_list->isNotEmpty()){
            foreach ($get_vehicle_type_list as $key => $get_vehicle_type){
                $vehicle_type_list[] = "<option value='".$get_vehicle_type->id ."'>".$get_vehicle_type->name."</option>";
            }
        }

        return response()->json([
            'success' => true,
            'vehicle_type_list' => $vehicle_type_list
        ]);
    }

    //Update or Store Transport Provider
    public function postUpdateTransportDriver(ProviderStoreRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $driver_details_id = $request->get('driver_details_id');

        if ($driver_details_id != Null) {
            $transport_driver = TransportDriverDetails::query()->where('id', $driver_details_id)->first();
            if ($transport_driver == Null) {
                Session::flash('error', 'Driver details not found!');
                return redirect()->back();
            }
            $provider_details = User::query()->where('id', $transport_driver->user_id)->first();
            if ($provider_details == Null) {
                Session::flash('error', 'Driver service details not found!');
                return redirect()->back();
            }
        } else {
            $provider_details = new User();
            $provider_details->verified_at = date('Y-m-d H:i:s');
            $provider_details->status = 1;
            $provider_details->is_register = 1;
            $provider_details->login_type = "email";
            $provider_details->is_driver_type = 1;
            $provider_details->is_driver_status = 1;
            $provider_details->driver_vehicle_status = 1;
            $provider_details->driver_doc_status = 1;
            $provider_details->driver_current_status = 1;
            $provider_details->active_mode = 2;
            /* ----------------------------------------- Access from City Admin ------------------------------------------*/
            if (request()->get("admin_role") == 4) {
                $provider_details->area_id = request()->get("admin_city_id");
            }
            /* ----------------------------------------- End Access from City Admin --------------------------------------*/
            $provider_details->save();

            $transport_driver = new TransportDriverDetails();
            $transport_driver->user_id = $provider_details->id;
            $transport_driver->total_request = 0;
            $transport_driver->total_completed = 0;
            $transport_driver->total_cancelled = 0;
            $transport_driver->rating = 0.00;
            $transport_driver->availability_ride_status = 1;
            $transport_driver->doc_status = 1;
            $transport_driver->current_lat = 0;
            $transport_driver->current_long = 0;
        }

        if((isset($provider_details->contact_number) && $provider_details->contact_number != Null && ($provider_details->country_code."".$provider_details->contact_number != $request->get('country_code')."".$request->get('contact_number') ))){
            $provider_details->access_token = "";
        }

        $provider_details->first_name = ucwords(trim($request->get('name')));
        $provider_details->email = $request->get('email');
        $provider_details->contact_number = $request->get('contact_number');
        $provider_details->country_code = $request->get('country_code');
        $provider_details->gender = $request->get('gender');

        if ($request->file('avatar')) {
            if (\File::exists(public_path('/assets/images/profile-images/customer/' . $provider_details->avatar))) {
                \File::delete(public_path('/assets/images/profile-images/customer/' . $provider_details->avatar));
            }
            $file = $request->file('avatar');
            $file_new = random_int(1, 99) . date('sihYdm') . random_int(1, 99) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/profile-images/customer/', $file_new);
            $provider_details->avatar = $file_new;
        }

        $provider_details->InviteCode($provider_details->id, $provider_details->first_name);
        $provider_details->save();

        $transport_driver->vehicle_type_id = $request->get('vehicle_type_id');
        $transport_driver->vehicle_company = $request->get('vehicle_company');
        $transport_driver->plat_no = $request->get('plat_no');
        $transport_driver->model_year = $request->get('model_year');
        $transport_driver->model_name = $request->get('model_name');
        $transport_driver->vehicle_color = $request->get('vehicle_color');

        if ($request->get("service_id") == 1){
            $transport_driver->child_seat = ($request->get("child_seat") == "on")? 1 : 0;
            $transport_driver->handicap = ($request->get("handicap") == "on")? 1 : 0;
        }

        $transport_driver->save();

        $path = public_path('/assets/images/provider-vehicle-image/');
        \File::isDirectory($path) or \File::makeDirectory($path, 755, true, true);
        foreach (['vehicle_image_front', 'vehicle_image_side', 'vehicle_image_rear', 'vehicle_image'] as $photoField) {
            if ($request->file($photoField)) {
                $existing = $transport_driver->{$photoField} ?? null;
                if ($existing && \File::exists(public_path('/assets/images/provider-vehicle-image/' . $existing))) {
                    \File::delete(public_path('/assets/images/provider-vehicle-image/' . $existing));
                }
                $file = $request->file($photoField);
                $file_new = random_int(1, 99) . date('siHYdm') . random_int(1, 99) . '_' . $photoField . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/assets/images/provider-vehicle-image/'), $file_new);
                $transport_driver->{$photoField} = $file_new;
            }
        }
        if ($transport_driver->vehicle_image_front) {
            $transport_driver->vehicle_image = $transport_driver->vehicle_image_front;
        }
        $transport_driver->save();


        if ($request->get('account_number') != Null && $request->get('holder_name') != Null && $request->get('bank_name') != Null && $request->get('bank_location') != Null && $request->get('payment_email') != Null && $request->get('bic_swift_code') != Null) {
            $provider_bank_details = ProviderBankDetails::query()->where('provider_id', $provider_details->id)->first();
            if ($provider_bank_details == Null) {
                $provider_bank_details = new ProviderBankDetails();
            }
            $provider_bank_details->provider_id = $provider_details->id;
            $provider_bank_details->account_number = $request->get('account_number');
            $provider_bank_details->holder_name = $request->get('holder_name');
            $provider_bank_details->bank_name = $request->get('bank_name');
            $provider_bank_details->bank_location = $request->get('bank_location');
            $provider_bank_details->payment_email = $request->get('payment_email');
            $provider_bank_details->bic_swift_code = $request->get('bic_swift_code');
            $provider_bank_details->save();
        }

        $id = $request->get('id');
        if ($id != Null) {
            $msg = 'Driver updated successfully!';
        } else {
            $msg = 'Driver added successfully!';
        }
        Session::flash('success', $msg);

        // check sub admin approved driver list permission
        if (Auth::guard("admin")->check() && Auth::guard("admin")->user()->roles == 4) {
            $subadmin_id = Auth::guard("admin")->user()->id;
            // get approved driver module id
            $get_approved_driver = AdminModule::query()->select('id')
                ->where('route_path', '=', 'get:admin:transport_service_approved_providers_list')
                ->where('status', '=', 1)
                ->first();

            if ($get_approved_driver != Null) {
                $check_approved_driver_module = AdminPermission::query()
                    ->select('id')
                    ->where('admin_id', '=', $subadmin_id)
                    ->where('module_id', '=', $get_approved_driver->id)
                    ->where('permission', '=', 1)
                    ->first();
                if ($check_approved_driver_module == Null) {
                    if ($id != Null) {
                        return redirect()->route('get:admin:edit_transport_service_driver', [$id]);
                    } else {
                        return redirect()->route('get:admin:add_transport_service_driver');
                    }
                }
            }
        }
        if ($provider_details->is_driver_status == 1) {
            $status = "approved";
        } elseif ($provider_details->is_driver_status == 0) {
            $status = "un-approved";
        } elseif ($provider_details->is_driver_status == 2) {
            $status = "blocked";
        }else{
            $status="rejected";
        }
        return redirect()->route('get:admin:transport_service_provider_list', $status);
    }

    public function getEditTransportDriver(Request $request, $driver_details_id)
    {
        $driver_detials = TransportDriverDetails::query()->select(
            'users.id as provider_id', 'users.first_name as name','users.first_name as lname', 'users.email', 'users.contact_number','users.country_code',
            'users.avatar', 'users.login_type', 'users.gender',
            'transport_driver_details.id as driver_details_id', 'transport_driver_details.rating',
            'transport_driver_details.vehicle_type_id', 'transport_driver_details.vehicle_company',
            'transport_driver_details.vehicle_image',
            'transport_driver_details.handicap', 'transport_driver_details.child_seat',
            'transport_vehicle_type.service_id',
            'transport_driver_details.plat_no', 'transport_driver_details.model_year', 'transport_driver_details.model_name',
            'transport_driver_details.vehicle_color', 'transport_driver_details.no_of_seat'
        )
            ->join('users', 'users.id', '=', 'transport_driver_details.user_id')
            ->join('transport_vehicle_type', 'transport_vehicle_type.id', '=', 'transport_driver_details.vehicle_type_id')
            ->whereNull('users.deleted_at')
            ->where('transport_driver_details.id', $driver_details_id)
            ->first();

        if ($driver_detials != Null) {

            $vehicle_services = VehicleService::query()->select('id','name')->where('status','=',1)->get();
            // if vehicle service enable then only vehicle type fetch
            if(!$vehicle_services->isEmpty()){
                $vehicle_services_ids = $vehicle_services->pluck('id');
                $vehicle_types = TransportVehicleType::query()->whereIn('service_id',$vehicle_services_ids)->where('status', '=', 1)->get();
            }
            $bank_details = ProviderBankDetails::query()->where('provider_id', $driver_detials->provider_id)->first();

            if(isset($vehicle_types) && !$vehicle_types->isEmpty()){
                $view = view('admin.pages.transport_services.providers.driver_form', compact( 'vehicle_types','vehicle_services', 'driver_detials', 'bank_details'));
            }else{
                $view = view('admin.pages.transport_services.providers.driver_form', compact( 'driver_detials', 'bank_details'));
            }
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        }
        else {
            Session::flash('error', 'Driver Not Found!');
            return redirect()->back();
        }
    }

    public function getTransportVehicleService(Request $request)
    {
        $vehicle_services = VehicleService::query()->get();
        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.vehicle_service.manage_vehicle_service', compact( 'vehicle_services'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.vehicle_service.manage_vehicle_service', compact( 'vehicle_services'));
    }

    public function getEditTransportVehicleService($id, Request $request)
    {
        $transport_vehicle_service = VehicleService::query()->where('id', $id)->first();
        $language_lists = LanguageLists::query()->where('status', '=', '1')->get();
        if ($transport_vehicle_service != Null) {
            $view = view('admin.pages.transport_services.vehicle_service.form_vehicle_service', compact( 'transport_vehicle_service','language_lists'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        } else {
            Session::flash('error', 'Vehicle Type Not Found!');
            return redirect()->back();
        }
    }

    public function getAddTransportVehicleService(Request $request)
    {
        $language_lists = LanguageLists::query()->where('status', '=', '1')->get();

        if ($request->ajax()) {
            $view = view('admin.pages.transport_services.vehicle_service.form_vehicle_service',compact( 'language_lists'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.transport_services.vehicle_service.form_vehicle_service',compact( 'language_lists'));
    }

    public function getDeleteTransportVehicleService(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false
            ]);
        }

        $vehicle_service = VehicleService::query()->where('id', '=', $id)->first();
        if ($vehicle_service == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        if (\File::exists(public_path('/assets/images/service-category/vehicle-service/' . $vehicle_service->icon_name))) {
            \File::delete(public_path('/assets/images/service-category/vehicle-service/' . $vehicle_service->icon_name));
        }
        $vehicle_service->delete();
        return response()->json([
            'success' => true
        ]);
    }

    public function getAjaxUpdateTransportVehicleServiceStatus(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $vehicle_service_details = VehicleService::query()->select('id', 'status')->where('id', '=', $id)->first();
        if ($vehicle_service_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($vehicle_service_details->status == 1) {
            $status = $vehicle_service_details->status = 0;
        } else {
            $status = $vehicle_service_details->status = 1;
        }
        $vehicle_service_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    public function postUpdateTransportVehicleService(Request $request) {

        if($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            "name" =>  Rule::unique('vehicle_services', 'name')->ignore($request->get('id')),
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->errors()->first());
            return redirect()->back();
        }

        $id = $request->get('id');
        if ($id != Null) {
            $vehicle_service = VehicleService::query()->where('id', $id)->first();
            Session::flash('success', 'Vehicle Service Updated successfully!');
        } else {
            $vehicle_service = new VehicleService();
            Session::flash('success', 'Vehicle Service Added successfully!');
        }

        $vehicle_service->name = $request->get('name');

        try {
            $language_list = LanguageLists::query()->select('language_name as name',
                DB::raw("(CASE WHEN language_code != 'en' THEN  concat(language_code,'_name') ELSE 'name' END) as category_col_name")
            )->where('status', 1)->get();
            if ($language_list->isNotEmpty()) {
                foreach ($language_list as $key => $language) {
                    if (Schema::hasColumn('vehicle_services', $language->category_col_name)) {
                        $vehicle_service->{$language->category_col_name} = $request->get($language->category_col_name);
                    }
                }
            }
        } catch (\Exception $e) {}

        $vehicle_service->cost_for_km = $request->get('cost_for_km');
        $vehicle_service->time_fare = $request->get('time_fare');
        $vehicle_service->max_bargain_percent = $request->get('max_bargain_percent');
        $vehicle_service->max_offer_percent = $request->get('max_offer_percent');
        $vehicle_service->min_fare = $request->get('min_fare');
        if ($request->file('icon')) {
            if (\File::exists(public_path('/assets/images/vehicle-service/' . $vehicle_service->icon_name))) {
                \File::delete(public_path('/assets/images/vehicle-service/' . $vehicle_service->icon_name));
            }
            $file = $request->file('icon');
            $file_new = $request->get('service_cat_id') . date('siHYdm') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/vehicle-service/', $file_new);
            $vehicle_service->icon_name = $file_new;
        }
        if ($request->file('vehicle_service_icon')) {
            if (\File::exists(public_path('/assets/images/vehicle-service/' . $vehicle_service->vehicle_service_icon))) {
                \File::delete(public_path('/assets/images/vehicle-service/' . $vehicle_service->vehicle_service_icon));
            }
            $file_service = $request->file('vehicle_service_icon');
            $file_new_service = $request->get('service_cat_id') . date('siHYdm') . '.' . $file_service->getClientOriginalExtension();
            $file_service->move(public_path() . '/assets/images/vehicle-service/', $file_new_service);
            $vehicle_service->vehicle_service_icon = $file_new_service;
        }
        $vehicle_service->courier_services = $request->get('courier_services');
        $vehicle_service->vehicle_service_description = $request->get('vehicle_service_description');
        $vehicle_service->service_mode = in_array($request->get('service_mode'), ['transport', 'delivery', 'expreso', 'encomiendas'], true)
            ? $request->get('service_mode')
            : 'transport';
        $vehicle_service->display_order = (int) ($request->get('display_order') ?? 0);
        $vehicle_service->status = $request->get('status');
        $vehicle_service->save();
        return redirect()->route('get:admin:vehicle_service');
    }

    //get Transport Service Cash Out List
    public function getTransportServiceCashOutList(Request $request) {
        $view = view('admin.pages.transport_services.cashouts.transport_cash_out_list');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }


    //get Ajax Transport Cash Out List
    public function getTransportCashOutList(Request $request){
        $status_check = $request->get('status_check');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $rowperpage = (isset($rowperpage) && $rowperpage > 0) ? $rowperpage : 25;
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $cashout_lists_check = CashOut::query()
            ->select('cash_out.id', 'cash_out.user_name','cash_out.amount','cash_out.status','cash_out.user_id','provider_bank_details.bank_name','provider_bank_details.account_number','provider_bank_details.payment_email','users.area_id')
            ->leftJoin('provider_bank_details','provider_bank_details.provider_id','=','cash_out.user_id')
            ->leftJoin('users', 'users.id', '=', 'cash_out.user_id')
            ->distinct('cash_out.id');
        $cashout_list_data = $cashout_lists_check;
        $totalRecords = $cashout_lists_check->count();
        if($searchValue != ""){
            $totalRecordswithFilter = $cashout_lists_check->where(function($query) use ($searchValue){
                $query->orWhere('user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('amount', 'like', '%' .$searchValue . '%');
            })->count();
        } else{
            $totalRecordswithFilter = $cashout_lists_check->count();
        }

        $record = $cashout_list_data;

        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $record->where('users.area_id', request()->get("admin_city_id"));
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/
        if($searchValue != ""){
            $record = $record->where(function($query) use ($searchValue){
                $query->orWhere('user_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('amount', 'like', '%' .$searchValue . '%');
            });
        }

        if ($columnName != 'no'){
            $record = $record->orderBy($columnName, $columnSortOrder);
        }else{
            $record = $record->orderBy('id', 'desc');
        }

        $records = $record->skip($start)
            ->take($rowperpage)
            ->get();

        $i = 1;
        $data_arr = [];
        foreach ($records as $record){
            if($record->status == 0){
                $cashout_status = "pending";
            } else if($record->status == 1){
                $cashout_status = "approved";
            } else if($record->status == 2){
                $cashout_status = "rejected";
            }
            $cashout_status_html = '<span class="'.$cashout_status.'" id="status_' . $record->id . '">'.$cashout_status.'</span>';
            $status_html='';
            if($record->status != 2){
                $status_html = '<a class="render_link" id="approve_remove_' . $record->id . '">
                                <img src="' . asset('/assets/images/template-images/thumbs-up.png') . '"
                                     style="width:20px; height: 20px;"
                                     data-toggle="tooltip" class="approve"
                                     id="' . $record->id . '"
                                     data-placement="top" title="Approve">
                          </a>
                          <a class="render_link" id="reject_remove_' . $record->id . '">
                                <img src="' . asset('/assets/images/template-images/thumb-down.png') . '"
                                    style="width:20px; height: 20px;"
                                    data-toggle="tooltip" class="reject"
                                    id="' . $record->id . '"
                                    data-placement="top" title="Reject">
                          </a>';
            }
            if($record->status == 1){
                $status_html = '<a class="render_link" id="reject_remove_' . $record->id . '">
                                <img src="' . asset('/assets/images/template-images/thumb-down.png') . '"
                                    style="width:20px; height: 20px;"
                                    data-toggle="tooltip" class="reject"
                                    id="' . $record->id . '"
                                    data-placement="top" title="Reject">
                          </a>';
            }



            $data_arr[] = array(
                "no" => $i,
                "user_name" => $record->user_name,
                "amount" => '<span class="currency"></span>'.$record->amount,
                "bank_name" => $record->bank_name,
                "account_number" => $record->account_number,
                "payment_email" => $record->payment_email,
                'status' => '<span class="order-status">'.$cashout_status_html.'</span>',
                'actions'=>$status_html
            );
            $i++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        return json_encode($response);
    }

    //get Ajax Transport Cashout Status Change
    public function getUpdateTransportCashOutStatus(Request $request)
    {
        if($this->is_restricted == 1){
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        $request_for = $request->get('request_for');
        if ($id == Null || $request_for == Null) {
            return response()->json([
                'success' => false,
                'message' => __('driver_messages.0')
            ]);
        }

        $cashout_details = CashOut::query()->where('id', $id)->first();
        if ($cashout_details == Null) {
            return response()->json([
                'success' => false,
                'message' => __('driver_messages.0')
            ]);
        }
        $user_details = User::query()->where('id', $cashout_details->user_id)->first();
        if ($user_details == Null) {
            return response()->json([
                'success' => false,
                'message' => __('driver_messages.0')
            ]);
        }
        $user_id=$cashout_details->user_id;
        $amount_to_default=$cashout_details->amount;
        if ($request_for == 1) {
            $cashout_details->status = 1;
            $cashout_details->save();
        } elseif ($request_for == 2) {

            //for get wallet Balance
            $last_amount = $this->notificationClass->getWalletBalance($user_id);

            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $user_id;
            $add_balance->transaction_type = 1;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject = "credit by admin";
            $add_balance->subject_code = 18;
            $add_balance->remaining_balance = floatval($last_amount + $amount_to_default);
            $add_balance->save();

            $cashout_details->status = 2;
            $cashout_details->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => __('driver_messages.0')
            ]);
        }
        $notification_log = $this->notificationClass->DriverCashOutNotification($user_details->device_token,$user_details->language,$request_for);
        return response()->json([
            'success' => true
        ]);
    }


}
