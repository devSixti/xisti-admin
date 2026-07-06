<?php

namespace App\Http\Controllers;

use App\Classes\AdminClass;
use App\Helpers\RideSessionHelper;
use App\Classes\UserClassApi;
use App\Classes\NotificationClass;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\EmailTemplatesRequest;
use App\Http\Requests\GeneralSettingsRequest;
use App\Http\Requests\HomePageTopServiceRequest;
use App\Http\Requests\PromocodeDetailsRequest;
use App\Http\Requests\RequiredDocumentsRequest;
use App\Http\Requests\ServiceCategoryRequest;
use App\Http\Requests\SubAdminRequest;
use App\Http\Requests\SosRequest;
use App\Models\Admin;
use App\Models\AdminAreaList;
use App\Models\AdminCategoryPermission;
use App\Models\AdminModule;
use App\Models\AdminPageAction;
use App\Models\AdminPermission;
use App\Models\AppVersionSetting;
use App\Models\AreawiseServiceCategory;
use App\Models\CityAreaSettings;
use App\Models\EmailTemplates;
use App\Models\GeneralSettings;
use App\Models\LanguageConstant;
use App\Models\LanguageLists;
use App\Models\PageSettings;
use App\Models\PromocodeDetails;
use App\Models\PushNotification;
use App\Models\PushEventTemplate;
use App\Helpers\PushEventTemplateHelper;
use App\Models\RestrictedArea;
use App\Models\SearchRadius;
use App\Models\TransportRideBook;
use App\Models\User;
use App\Models\UserRatings;
use App\Models\UserReferHistory;
use App\Models\UserWalletTransaction;
use App\Models\Provider;
use App\Models\ProviderDocuments;
use App\Models\ProviderServices;
use App\Models\RequiredDocuments;
use App\Models\ServiceCategory;
use App\Models\ServiceSettings;
use App\Models\TransportVehicleType;
use App\Models\UserAddress;
use App\Models\VehicleService;
use App\Models\WorldCurrency;
use App\Models\Sos;
use App\Models\SosTriggerLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    private $adminClass;
    private $notificationClass;
    private $transport_ride_status;
    private $is_restricted = 0;


    public function __construct(AdminClass $adminClass, NotificationClass $notificationClass)
    {
        $this->middleware('auth');
        $this->adminClass = $adminClass;
        $this->notificationClass = $notificationClass;
        $this->transport_ride_status = ['pending', 'accepted', 'schedule-accepted', 'arrived', 'cancelled', 'running', 'drop', 'payment', 'rating', 'completed', 'failed'];

        $this->middleware(function ($request, $next) {
            $is_restrict_admin = $request->get('is_restrict_admin');
            $this->is_restricted = $is_restrict_admin;
            return $next($request);
        });

    }

    public function getAdminTest_Mail(Request $request)
    {

        $email = "abc@gmail.com";
        Config::set('mail.default','smtp');
        Config::set('mail.mailers.smtp.username','email');
        Config::set('mail.mailers.smtp.password','pass');
        Config::set('mail.mailers.smtp.host','smtp.googlemail.com');
        Config::set('mail.mailers.smtp.port','465');
        Config::set('mail.mailers.smtp.encryption','ssl');
        $a = Config::get('mail.mailers.smtp');
//        dd($a);
        $data = [
            "mail_type" => 1,
            "user_name" => "Av",
            "content_1" => "",
        ];
        try {
            Mail::send('success', $data, function ($message) use ($email) {
                $message->to($email);
                $message->subject('Welcome to Mycheckout App');
            });
        }catch(\Exception $e){
            dd($e->getMessage());
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.dashboard')->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.dashboard');
//        return view('home');
    }


    //dashboard
    public function getAdminDashboard(Request $request)
    {
        if (Auth::guard("admin")->user()->roles == 1 || Auth::guard("admin")->user()->roles == 4) {

            $admin_role = request()->get("admin_role");
            $admin_city_id = request()->get("admin_city_id");

            $date = date('Y-m-d');

            //total completed order
            $transport_completed = TransportRideBook::query()->where('status', 9)->whereDate('created_at', '=', $date);
            //check area wise
            if ($admin_role == 4) {
                $transport_completed->where('area_id', $admin_city_id);
            }
            $transport_completed = $transport_completed->count();
            $total_completed_order = ($transport_completed);


            //total cancelled order
            $transport_cancelled = TransportRideBook::query()->whereIn('status', [4])->whereDate('created_at', '=', $date);

            if ($admin_role == 4) {
                $transport_cancelled->where('area_id', $admin_city_id);
            }

            $transport_cancelled = $transport_cancelled->count();

            $total_cancelled_order = ($transport_cancelled);


            //total order
            $transport_total_order = TransportRideBook::query()->whereNotIn('status', [10])->whereDate('created_at', '=', $date);

            if ($admin_role == 4) {
                $transport_total_order->where('area_id', $admin_city_id);
            }

            $transport_total_order = $transport_total_order->count();

            $total_order = ($transport_total_order );


            //total completed order
            $transport_revenue = TransportRideBook::query()->where('status', 9)->whereDate('created_at', '=', $date);

            if ($admin_role == 4) {
                $transport_revenue->where('area_id', $admin_city_id);
            }

            $transport_rev = $transport_revenue->sum('admin_commission');


            $n_tran = (0 + str_replace(",", "", number_format($transport_rev, 2)));


            $n = ($n_tran );
            if (!is_numeric($n)) {
                false;
            }
            if ($n > 1000000000000) {
                $total_revenue = round(($n / 1000000000000), 2) . ' T';
            } elseif ($n > 1000000000) {
                $total_revenue = round(($n / 1000000000), 2) . ' B';
            } elseif ($n > 1000000) {
                $total_revenue = round(($n / 1000000), 2) . ' M';
            } elseif ($n > 1000) {
                $total_revenue = round(($n / 1000), 2) . ' K';
            } else {
                $total_revenue = $n;
            }
            $view = view('admin.pages.super_admin.dashboard', compact('total_revenue', 'total_completed_order', 'total_cancelled_order', 'total_order'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        }
        elseif (Auth::guard("admin")->user()->roles == 3) {
            return redirect()->route('get:account:dashboard');
        }
        elseif (Auth::guard("admin")->user()->roles == 2) {
            return redirect()->route('get:dispatcher:manual_ride_booking');
        }
        else {
            Auth::guard('admin')->logout();
            return redirect()->route('get:admin:login');
        }
    }

    //user list
    public function getAdminUserList(Request $request)
    {
        $view = view('admin.pages.super_admin.user.manage');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminUserListNew(Request $request)
    {
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

        $totalRecords = User::query()->select('count(*) as allcount');
        $admin_role = request()->get("admin_role");
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $totalRecords->where('area_id', $admin_city_id);
        }
        $totalRecords = $totalRecords->count();

        if ($searchValue != "") {
            $totalRecordswithFilter = User::query()->select('count(*) as allcount');
            $admin_role = request()->get("admin_role");
            if ($admin_role == 4) {
                $admin_city_id = request()->get("admin_city_id");
                $totalRecordswithFilter->where('area_id', $admin_city_id);
            }
            $totalRecordswithFilter = $totalRecordswithFilter->where('first_name', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%')
                ->orWhere('contact_number', 'like', '%' . $searchValue . '%')
                ->where('is_register',1)
                ->count();
        } else {
            $totalRecordswithFilter = User::query()->select('count(*) as allcount');
            $admin_role = request()->get("admin_role");
            if ($admin_role == 4) {
                $admin_city_id = request()->get("admin_city_id");
                $totalRecordswithFilter->where('area_id', $admin_city_id);
            }
            $totalRecordswithFilter = $totalRecordswithFilter->where('is_register',1)->count();
        }

        $records = User::query()->select('users.*')->where('users.is_register',1);
        $admin_role = request()->get("admin_role");
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $records->where('area_id', $admin_city_id);
        }
        if ($columnName != 'id'){
            $records = $records->orderBy($columnName, $columnSortOrder);
        }else{
            $records = $records->orderBy('users.id', 'desc');
        }

        if ($searchValue != "") {
            $records = $records->where('users.first_name', 'like', '%' . $searchValue . '%');
            $records = $records->orWhere('users.email', 'like', '%' . $searchValue . '%');
            $records = $records->orWhere('users.contact_number', 'like', '%' . $searchValue . '%');
        }
        $records = $records
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $key => $record) {
            //$id = $record->id;
            $id = $key + 1 + $start;
            $username = $record->first_name . " " . $record->last_name;
            $email = $record->email;
            $country_code = $record->country_code;
            $contact_number = $record->contact_number;
            $user_app_version = ($record->app_version != Null) ? $record->app_version : 0;

            $user_wallet = UserWalletTransaction::query()->select('remaining_balance')->where('user_id', $record->id)->where('wallet_provider_type',0)->orderBy('id', 'desc')->first();
            if ($user_wallet != Null) {
                $user_wallet_balance = round($user_wallet->remaining_balance,2);
            } else {
                $user_wallet_balance = 0;
            }

            $refer_count = UserReferHistory::query()->where('refer_id',$record->id)->count();

            $user_wallet_balance_html = '<span id="change_wallet_' . $record->id . '">' . $user_wallet_balance . '</span><a href="' . route('post:admin:customer_wallet_transaction', ['id' => $record->id]) . '" userid="' . $record->id . '" style="margin: 0 7px;">
                            <img src="' . asset('/assets/images/template-images/wallet-history3.png') . '" style="width:25px; height: 25px;" title="' . e(__('admin.pages.wallet_transaction')) . '">
                        </a>
                        <a style="border: 1px solid Green; border-radius: 5px; font-size: 16px; font-weight: bolder; color: green; padding: 0 5px;cursor: pointer" class="md-trigger-1 text-c-orenge"
                              data-modal="modal-3" data-toggle="tooltip" userid="' . $record->id . '"> <i class="fa fa-plus" aria-hidden="true"></i> / <i class="fa fa-minus" aria-hidden="true"></i> </a>';

            $action = '<a  href="' . route('get:admin:edit_user', $record->id) . '" style="margin: 0 7px;">
                            <img src="' . asset('/assets/images/template-images/writing-1.png') . '" style="width:20px; height: 20px;" title="' . e(__('admin.common.edit')) . '">
                        </a>
                        <a class="delete" userid="' . $record->id . '" style="margin: 0 7px; cursor: pointer;">
                            <img src="' . asset('/assets/images/template-images/remove-1.png') . '" style="width:20px; height: 20px;" title="' . e(__('admin.common.delete')) . '">
                        </a>

               ';
            $checked = ($record->status == "1") ? "checked" : "";
            $user_Status = \App\Helpers\AdminUi::activeStatusLabel($record->status);
            $status = '<span class="toggle">
                            <label>
                                <input name="status"
                                       class="form-control user"
                                       id="user_id_' . $record->id . '"
                                       user_id="' . $record->id . '"
                                       user_status="' . $record->status . '"
                                       type="checkbox"   ' . $checked . ' >
                                <span class="button-indecator" data-toggle="tooltip"s
                                      data-placement="top"
                                      id="title_status_' . $record->id . '"
                                      title="' . $user_Status . '"></span>
                            </label>
                        </span>';

            $refer_stat = '<a href="'.route('get:admin:referred_list',['id'=>$record->id]) .'" class="url-link" title="'.e(__('admin.pages.referred_list')).'">
                            ' . e(__('admin.common.view')) . ' ('.$refer_count.')</a>';

            $data_arr[] = array(
                "id" => $id,
                "first_name" => $username,
                "email" => User::Email2Stars($email),
                "contact_number" => User::ContactNumber2Stars($country_code . $contact_number),
                "wallet_balance" => $user_wallet_balance_html,
                "refer_stats" => $refer_stat,
                'status' => $status,
                'user_app_version' => $user_app_version,
                'action' => $action
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return json_encode($response);
    }

    //add user form
    public function getAdminAddUser(Request $request)
    {
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.user.form')->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.user.form');
    }

    //edit user
    public function getAdminEditUser(Request $request, $id)
    {
        if (!is_numeric($id) || $id == Null) {
            Session::flash('error', 'Customer Not found!');
            return redirect()->back();
        }
        $user_details = User::query()->where('id', '=', $id)->first();
        if ($user_details == Null) {
            $view = view('admin.pages.super_admin.user.form');
        } else {
            $view = view('admin.pages.super_admin.user.form', compact('user_details'));
        }
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //save or update user
    public function postAdminUpdateUser(CustomerStoreRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $user = User::query()->where('id', $request->get('id'))->first();
        $msg = "Customer Updated successfully!";
        if ($user == Null) {
            $user = new User();
            $user->verified_at = date('Y-m-d H:i:s');
            $user->is_register = 1;
//            $user->password = Hash::make($request->get('password'));
            $msg = "Customer added successfully!";
        }

        if((isset($user->contact_number) && $user->contact_number != Null && ($user->country_code."".$user->contact_number != $request->get('country_code')."".$request->get('contact_number')))){
            $user->access_token = Null;
        }
        $user->first_name = ucwords(strtolower(trim($request->get('first_name'))));
//        $user->last_name = ucwords(strtolower(trim($request->get('last_name'))));
        $user->email = $request->get('email');
        $user->country_code = $request->get('country_code');
        $user->contact_number = trim($request->get('contact_number'));
        $user->login_type = "email";


        if ($request->file('avatar') != Null) {
            if (\File::exists(public_path('/assets/images/profile-images/customer/' . $user->avatar))) {
                \File::delete(public_path('/assets/images/profile-images/customer/' . $user->avatar));
            }
            $file = $request->file('avatar');
            $file_new = rand(1, 9) . date('siHYdm') . rand(1, 9) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/profile-images/customer/', $file_new);
            $user->avatar = $file_new;
        }
//        $user->gender = $request->get('gender');
        $admin_role = request()->get("admin_role");
        if ($admin_role == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $user->area_id = $admin_city_id;
        }
        $user->save();
        if($request->get('id') == Null)
        {
            $user->generateAccessToken($user->id);
            $user->InviteCode($user->id, $user->first_name);
        }

        Session::flash('success', $msg);
        return redirect()->route('get:admin:user_list');
    }

    //delete user
    public function getAdminDeleteUser(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Customer Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user = User::where('id', $id)->first();
        if ($user == Null) {
            Session::flash('error', 'Customer Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }


        RideSessionHelper::reconcileForUser((int) $id);

        if (RideSessionHelper::hasBlockingRideActivity((int) $id)) {
            return response()->json([
                "success" => false,
                "message" => "Sorry, Currently the ride of this user is running or has a pending payment so you can't delete the account at the time. Try Later!"
            ]);
        }

        if($user->fix_user_show == 1){
            return response()->json([
                "success" => false,
                "message" => "Sorry,You cannot delete this user"
            ]);
        }

        try {
            app(UserClassApi::class)->forfeitWalletBalanceOnAccountDeletion((int) $id);

            if ($user->avatar && \File::exists(public_path('/assets/images/profile-images/customer/' . $user->avatar))) {
                \File::delete(public_path('/assets/images/profile-images/customer/' . $user->avatar));
            }
            $user->delete();
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => __('admin.errors.delete_customer_failed'),
            ]);
        }

        Session::flash('success', 'Customer remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    //get Ajax Transport Vehicle Type Status Change
    public function getAdminUpdateUserStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $user_details = User::query()->where('id', '=', $id)->first();
//        $user_details = User::select('id', 'status')->where('id', '=', $id)->first();
        if ($user_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        if ($user_details->status == 1) {
            $user_running_ride = TransportRideBook::query()->where('user_id', '=', $user_details->id)
                ->where(function ($query) {
                    $query->whereNotIn('status', [4, 9, 10])
                        ->orWhere(function($query2){
                            $query2->where('status',9)->where('payment_status',0);
                        });
                })->count();

            // check if user as register in driver and ride is running then not disbale
            $driver_running_ride = TransportRideBook::query()->where('driver_id', '=', $user_details->id)->whereNotIn('status', [4, 9, 10])->count();
            if ($user_running_ride > 0 || $driver_running_ride > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Sorry, Currently the ride of this user is running so you can't block the account at this time. Try Later!"
                ]);
            }
            $status = $user_details->status = 0;
            $user_details->access_token = Null;
            $user_details->device_token = Null;
            $general_settings = request()->get("general_settings");
            if ($general_settings != Null) {
                if ($general_settings != Null) {
                    if ($general_settings->send_mail == 1) {
                        $user_name = $user_details->first_name . " " . $user_details->last_name;
                        try {
                            $mail_type = "account_blocked_-_customer";
                            $to_mail = $user_details->email;
                            $subject = "Your Account has been Block by Admin";
                            $disp_data = array("##user_name##" => $user_name);
                            $mail_return_data = $this->notificationClass->sendMail($subject, $to_mail, $mail_type, $disp_data);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        } else {
            $status = $user_details->status = 1;
        }
        $user_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    //user review lists
    public function getAdminUserReviewList(Request $request, $user_id = "0")
    {
        if ($user_id != "") {
            $user_review_lists = UserRatings::query()->select('user_rating.id', 'user_rating.user_id as  user_id', 'user_rating.rating', 'user_rating.comment', 'user_rating.status as status', DB::raw("providers.first_name as provider_name")
                , 'service_category.name as service_name')
                ->Join('providers', 'providers.id', 'provider_id')
                ->Join('users', 'users.id', 'user_id')
                ->leftJoin('provider_services', 'provider_services.provider_id', 'providers.id')
                ->leftJoin('service_category', 'provider_services.service_cat_id', 'service_category.id')
                ->where('user_rating.user_id', $user_id)
                ->whereNull('users.deleted_at')
                ->whereNull('providers.deleted_at')
//                ->where('user_rating.status',1)
                ->groupBy('user_rating.id')
                ->get();
            $user_details = User::query()->select('first_name', 'last_name')->where('id', $user_id)->first();

            if ($user_review_lists != null) {
                if (count($user_review_lists) > 0) {
                    return view('admin.pages.super_admin.user.user_review', compact('user_review_lists', 'user_details'));
                } else {
                    return redirect()->route('get:admin:user_list')->with('error', 'Sorry, user review not found!!!');
                }
            } else {
                return redirect()->route('get:admin:user_list')->with('error', 'Sorry, user review not found!!!');
            }
        } else {
            return redirect()->route('get:admin:user_list')->with('error', 'Sorry, user review not found!!!');
        }

    }

    //get Ajax Transport Vehicle Type Status Change
    public function getAdminUpdateUserReviewStatus(Request $request)
    {
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user_review_details = UserRatings::query()->where('id', '=', $id)->first();
//        $user_review_details = User::select('id', 'status')->where('id', '=', $id)->first();
        if ($user_review_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($user_review_details->status == 1) {
            $status = $user_review_details->status = 0;
        } else {
            $status = $user_review_details->status = 1;
        }
        $user_review_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    public function getAdminDeleteUserReview(Request $request)
    {
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Customer Review Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user_ratting = UserRatings::where('id', $id)->first();
        $user_id = $user_ratting->user_id;
        if ($user_ratting == Null) {
            Session::flash('error', 'Customer Review Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user_ratting->delete();
        $user = User::query()->where('id', $user_id)->first();
        if ($user != null) {
            $avg_ratting = UserRatings::query()->where('user_id', $user_id)->average('rating');
            $user->rating = $avg_ratting;
            $user->save();
        }
        Session::flash('success', 'Customer Review remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }


    //get ajax admin required document status change
    public function getAjaxUpdateAdminRequiredDocumentStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $required_document_details = RequiredDocuments::select('id', 'status')->where('id', '=', $id)->first();
        if ($required_document_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($required_document_details->status == 1) {
            $status = $required_document_details->status = 0;
        } else {
            $status = $required_document_details->status = 1;
        }
        $required_document_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }


    //contains document Expiry

    public function getAjaxUpdateAdminRequiredDocumentExpiryStatus(Request $request)
    {
        $id = $request->get('id');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $required_document_details = RequiredDocuments::query()->where('id', '=', $id)->first();
        if ($required_document_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($required_document_details->contains_expiry == 1) {
            $contains_expiry = $required_document_details->contains_expiry = 0;
        } else {
            $contains_expiry = $required_document_details->contains_expiry = 1;
        }
        $required_document_details->save();
        return response()->json([
            'success' => true,
            'contains_expiry' => $contains_expiry,
        ]);
    }

    //get ajax admin required document status change
    public function getAjaxUpdateAdminApprovedRejectProviderDocument(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        $status = $request->get('status');
        if ($id == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $provider_document_details = ProviderDocuments::query()->where('id', '=', $id)->first();
        if ($provider_document_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
//        if ($provider_document_details->status == 1) {
//            $status = $provider_document_details->status = 0;
//        } else {
        $provider_document_details->status = $status;
//        }
        $provider_document_details->save();

        $driver = User::query()->select('users.device_token', 'users.language', 'users.login_device')
            ->where('users.id', '=', $provider_document_details->user_id)
            ->first();

        if($status == 1){
            $this->notificationClass->driverApproveDocumentNotification($id, $driver->device_token, __('driver_messages.91', [], $driver->language),__('driver_messages.369', [], $driver->language), 0, (int) $driver->login_device);
        } elseif ($status == 2){
            $this->notificationClass->driverRejectDocumentNotification($id, $driver->device_token, __('driver_messages.91', [], $driver->language),__('driver_messages.368', [], $driver->language), (int) $driver->login_device);
        }
        return response()->json([
            'success' => true,
            'status' => $provider_document_details,
        ]);
    }

    //get Admin General Setting
    public function getAdminGeneralSetting(Request $request)
    {
        $general_settings = request()->get("general_settings");
        if ($general_settings == null || !$general_settings->exists) {
            $general_settings = GeneralSettings::query()->first();
            if ($general_settings == null) {
                $general_settings = new GeneralSettings();
            }
        }
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.general_settings.form', compact('general_settings'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.general_settings.form', compact('general_settings'));
    }

    //save or update general settings
    public function postAdminUpdateGeneralSetting(GeneralSettingsRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $id = $request->get('id');
        if ($id != Null) {
            $general_settings = GeneralSettings::query()->first();
        } else {
            $general_settings = new GeneralSettings();
        }
        $general_settings->website_name = $request->get('website_name');

        if ($request->file('website_logo')) {
            if (\File::exists(public_path('/assets/images/website-logo-icon/' . $general_settings->website_logo))) {
                \File::delete(public_path('/assets/images/website-logo-icon/' . $general_settings->website_logo));
            }
            $file = $request->file('website_logo');
            $file_new = random_int(1, 99) . date('siHYdm') . random_int(1, 99) . '.' . $file->getClientOriginalExtension();
//            $file_new = 'logo.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/website-logo-icon/', $file_new);
            $general_settings->website_logo = $file_new;
        }

        if ($request->file('website_favicon')) {
            if (\File::exists(public_path('/assets/images/website-logo-icon/' . $general_settings->website_favicon))) {
                \File::delete(public_path('/assets/images/website-logo-icon/' . $general_settings->website_favicon));
            }
            $file = $request->file('website_favicon');
            $file_new = random_int(1, 99) . date('siHYdm') . random_int(1, 99) . '.' . $file->getClientOriginalExtension();
//            $file_new = 'favicon.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/images/website-logo-icon/', $file_new);
            $general_settings->website_favicon = $file_new;
        }

        if ($request->get('used_user_discount') > 0 && in_array($request->get('used_user_discount_type'), [1, 2])) {
            $general_settings->used_user_discount = round($request->get('used_user_discount'), 2);
            $general_settings->used_user_discount_type = $request->get('used_user_discount_type');
        } else {
            $general_settings->used_user_discount = 0;
            $general_settings->used_user_discount_type = 0;
        }
        if ($request->get('refer_user_discount') > 0 && in_array($request->get('refer_user_discount_type'), [1, 2])) {
            $general_settings->refer_user_discount = round($request->get('refer_user_discount'), 2);
            $general_settings->refer_user_discount_type = $request->get('refer_user_discount_type');
        } else {
            $general_settings->refer_user_discount = 0;
            $general_settings->refer_user_discount_type = 0;
        }

        $general_settings->address = $request->get('address');
        $general_settings->contact_no = $request->get('contact_no');
        $general_settings->email = $request->get('email');
        $general_settings->send_receive_email = $request->get('send_receive_email');
        $general_settings->copy_right = $request->get('copy_right');

        $general_settings->facebook_link = $request->get('facebook_link');
        $general_settings->instagram_link = $request->get('instagram_link');
        $general_settings->linkedin_link = $request->get('linkedin_link');
//        $general_settings->twitter_link = $request->get('twitter_link');

        $general_settings->user_playstore_link = $request->get('user_playstore_link');
        $general_settings->user_appstore_link = $request->get('user_appstore_link');

        //driver min wallet balance
        $general_settings->auto_settle_wallet = $request->get('autosettle_Module');
        $general_settings->driver_min_amount = $request->get('driver_min_amount');
        //min & max cash-out
        $general_settings->min_cashout = $request->get('min_cashout');
        $general_settings->max_cashout = $request->get('max_cashout');

       //payment method
        $cash_payment  =  $request->get('cash_payment');
        $card_payment  =  $request->get('card_payment');
        $wallet_payment = $request->get('wallet_payment');

        if($cash_payment == 0 && $card_payment == 0 && $wallet_payment == 0){
            Session::flash('error', 'Atleast one payment option need to be ON.');
            return redirect()->back();
        }
        //code for Payment Methods
        $general_settings->cash_payment = $cash_payment;
        $general_settings->wallet_payment = $wallet_payment;
        $general_settings->card_payment = $card_payment;

        //code for social login on/off
        $general_settings->is_google_login = $request->get('is_google_login');
        $general_settings->is_facebook_login = $request->get('is_facebook_login');
        $general_settings->is_apple_login = $request->get('is_apple_login');
        $general_settings->is_finger_login = $request->get('is_finger_login');

        $general_settings->doc_expiry_warning_one = $request->get('doc_expiry_warning_one');
        $general_settings->doc_expiry_warning_two = $request->get('doc_expiry_warning_two');
        $general_settings->doc_expiry_warning_three = $request->get('doc_expiry_warning_three');
        $general_settings->is_toll_module = $request->get('is_toll_module');
        $general_settings->charge_per_toll = $request->get('charge_per_toll');
        $general_settings->driver_price_suggestion = $request->get('driver_price_suggestion');
        $general_settings->fare_negotiation_step = max(1, (int) $request->get('fare_negotiation_step', 500));
        $general_settings->vat_rate_on_commission = max(0, (float) $request->get('vat_rate_on_commission', 19));
        $general_settings->driver_cancel_until_status = min(9, max(0, (int) $request->get('driver_cancel_until_status', 3)));
        $general_settings->enable_expreso_mobile = $request->has('enable_expreso_mobile') ? 1 : 0;
        $general_settings->enable_encomiendas_mobile = $request->has('enable_encomiendas_mobile') ? 1 : 0;
        $general_settings->require_courier_package_dimensions_mobile = $request->has('require_courier_package_dimensions_mobile') ? 1 : 0;
        $general_settings->enable_xisti_new_home_layout = $request->has('enable_xisti_new_home_layout') ? 1 : 0;
        $paymentCodes = $request->input('destination_payment_code', []);
        if (is_array($paymentCodes) && count($paymentCodes) > 0) {
            $catalog = \App\Helpers\DestinationPaymentHelper::buildCatalogFromAdminRows(
                $paymentCodes,
                $request->input('destination_payment_label_es', []),
                $request->input('destination_payment_label_en', [])
            );
            $general_settings->destination_payment_methods = $catalog !== []
                ? json_encode($catalog, JSON_UNESCAPED_UNICODE)
                : null;
        } else {
            $destinationMethodsRaw = trim((string) $request->get('destination_payment_methods', ''));
            if ($destinationMethodsRaw === '') {
                $general_settings->destination_payment_methods = null;
            } else {
                $decoded = json_decode($destinationMethodsRaw, true);
                if (!is_array($decoded)) {
                    Session::flash('error', 'Destination payment methods must be valid JSON.');
                    return redirect()->back();
                }
                $general_settings->destination_payment_methods = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }
        }
        $general_settings->save();
        if ($id != Null) {
            Session::flash('success', 'General Setting Updated successfully!');
        } else {
            Session::flash('success', 'General Setting Added successfully!');
        }
        return redirect()->route('get:admin:general_setting');
    }

    //App Version Setting
    public function getAdminAppVersionSetting(Request $request)
    {
        //User App Setting
        //Start android flutter user app version
        $is_android_flutter_user_app_version_check = 0;
        $android_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 3)->where("forcefully_type", "=", 1)->orderBy("id", "desc")->first();
        if ($android_flutter_user_app_version == Null) {
            $android_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 3)->orderBy("id", "desc")->first();
        } else {
            $is_android_flutter_user_app_version_check = 1;
        }
        $android_flutter_user_app_version_id = 0;
        if ($android_flutter_user_app_version != Null) {
            $android_flutter_user_app_version_id = $android_flutter_user_app_version->id;
        }
        $android_flutter_user_app_version_list = AppVersionSetting::query()->select('id', 'version_code', 'version_name', DB::raw("(CASE WHEN id != 0 THEN (CASE WHEN id = $android_flutter_user_app_version_id THEN 1 ELSE 0 END) ELSE 0 END) as is_selected"))
            ->where("app_type", "=", 0)
            ->where("app_device_type", "=", 3)
            ->get();
        //End android flutter user app version

        //Start ios flutter user app version
        $is_ios_flutter_user_app_version_check = 0;
        $ios_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 4)->where("forcefully_type", "=", 1)->orderBy("id", "desc")->first();
        if ($ios_flutter_user_app_version == Null) {
            $ios_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 4)->orderBy("id", "desc")->first();
        } else {
            $is_ios_flutter_user_app_version_check = 1;
        }
        $ios_flutter_user_app_version_id = 0;
        if ($ios_flutter_user_app_version != Null) {
            $ios_flutter_user_app_version_id = $ios_flutter_user_app_version->id;
        }
        $ios_flutter_user_app_version_list = AppVersionSetting::query()->select('id', 'version_code', 'version_name', DB::raw("(CASE WHEN id != 0 THEN (CASE WHEN id = $ios_flutter_user_app_version_id THEN 1 ELSE 0 END) ELSE 0 END) as is_selected"))
            ->where("app_type", "=", 0)
            ->where("app_device_type", "=", 4)
            ->get();
        //End ios flutter user app version

        $view = view('admin.pages.super_admin.app_version_setting.form', compact(
            'android_flutter_user_app_version_list', 'is_android_flutter_user_app_version_check', 'ios_flutter_user_app_version_list', 'is_ios_flutter_user_app_version_check'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminUpdateAppVersionSetting(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        //update android flutter user app version
        $android_flutter_user_app_version_id = $request->get("android_flutter_user_app_version");
        $android_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 3)->where("id", "=", $android_flutter_user_app_version_id)->first();
        if ($android_flutter_user_app_version != Null) {
            $android_flutter_user_app_version->forcefully_type = 0;
            if ($request->get('update_forcefully_android_flutter_user_app') != Null) {
                $android_flutter_user_app_version->forcefully_type = 1;
            }
            $android_flutter_user_app_version->save();
        }

        //update ios flutter user app version
        $ios_flutter_user_app_version_id = $request->get("ios_flutter_user_app_version");
        $ios_flutter_user_app_version = AppVersionSetting::query()->where("app_type", "=", 0)->where("app_device_type", "=", 4)->where("id", "=", $ios_flutter_user_app_version_id)->first();
        if ($ios_flutter_user_app_version != Null) {
            $ios_flutter_user_app_version->forcefully_type = 0;
            if ($request->get('update_forcefully_ios_flutter_user_app') != Null) {
                $ios_flutter_user_app_version->forcefully_type = 1;
            }
            $ios_flutter_user_app_version->save();
        }

        Session::flash('success', 'App Version Setting Updated successfully!');
        return redirect()->route('get:admin:app_version_setting');
    }

    //add provider services
    public function getAdminAddProviderServices(Request $request, $category_type, $provider_id)
    {
        $providers = Provider::select('providers.id', DB::raw("providers.first_name as name"), 'providers.email', 'providers.contact_number')
            ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
            ->where('providers.id', $provider_id)
            ->whereNull('providers.deleted_at')
            ->orderBy('providers.id', 'desc')
            ->first();

        $providers_services = ProviderServices::where('provider_id', $providers->id)->get();
        if ($category_type == "transport") {
            $service_category_multiple = ServiceCategory::select('id', 'name')->whereIn('category_type', [1, 5])->get();
        }

        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.provider.form_provider_service', compact('category_type', 'service_category_multiple'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.provider.form_provider_service', compact('category_type', 'service_category_multiple'));
    }

    public function postAdminCustomerOrderList(Request $request, $id)
    {
        $user_details = User::select('id', 'first_name', 'last_name', 'email', 'contact_number', 'updated_at', 'status')
            ->where('id', $id)
            ->first();
        if ($user_details == Null) {
            Session::flash('error', 'user details not found!');
            return redirect()->back();
        }
        $transport_order_list = TransportRideBook::select('user_ride_booking.id', 'service_category.name as service_cat_name', 'service_category.category_type as service_cat_type', 'service_category.icon_name as service_cat_icon', 'user_ride_booking.ride_no as order_no', 'user_ride_booking.total_pay', 'user_ride_booking.payment_type', 'user_ride_booking.status')
            ->join('service_category', 'service_category.id', '=', 'user_ride_booking.service_cat_id')
            ->where('user_ride_booking.user_id', $user_details->id)->get();

        $transport_ride_status = $this->transport_ride_status;

        $view = view('admin.pages.super_admin.user.order_list', compact('user_details', 'transport_order_list','transport_ride_status'));

        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminCustomerWalletTransaction(Request $request, $id)
    {
        if (!is_numeric($id) || $id == Null) {
            Session::flash('error', 'Customer wallet transaction Not found!');
            return redirect()->back();
        }
        $user_details = User::select('id', 'first_name', 'last_name', 'email', 'contact_number', 'status')
            ->where('id', $id)
            ->first();
        if ($user_details == Null) {
            Session::flash('error', 'Customer wallet transaction Not found!');
            return redirect()->back();
        }

        $wallet_transaction_list = UserWalletTransaction::select(
            'id', 'amount', 'subject', 'remaining_balance', 'created_at',
            DB::raw("(CASE WHEN transaction_type = 1 THEN 'Credit' ELSE (CASE WHEN transaction_type = 2 THEN 'Debit' ELSE '----' END) END) as transaction_type")
        )->where('user_id', $id)->orderBy('id', 'desc')->get();

        $view = view('admin.pages.super_admin.user.wallet_transaction', compact('user_details', 'wallet_transaction_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminUpdateCustomerWalletTransaction(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "wallet_amount" => "required",
                "choose_option" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $user_id = $request->get('user_id');
        $user = User::query()->select('id')
            ->where('id', $user_id)->first();
        if ($user == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $amount_to_default = round($request->get('wallet_amount'), 2);
        //get wallet balance
        $last_amount = $this->notificationClass->getWalletBalance($user_id);

        if ($request->get('choose_option') == 1) {
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $user_id;
            $add_balance->wallet_provider_type = 0;
            $add_balance->transaction_type = 1;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject_code = 6;
            $add_balance->subject = "credit by Admin";
            $add_balance->remaining_balance = floatval($last_amount + $amount_to_default);
            $add_balance->save();
        } else {
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $user_id;
            $add_balance->wallet_provider_type = 0;
            $add_balance->transaction_type = 2;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject_code = 13;
            $add_balance->subject = "debit by Admin";
            $add_balance->remaining_balance = floatval($last_amount - $amount_to_default);
            $add_balance->save();
        }

        $last_amount = $add_balance->remaining_balance;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'user_id' => $user->id,
            'last_amount' => $last_amount
        ]);
    }

    //get delivery person list
    public function getRequiredDocumentList(Request $request)
    {
        $required_documents_list = RequiredDocuments::query()->select('required_documents.id', 'required_documents.name as document_name',
            'required_documents.status','required_documents.contains_expiry')->get();

        $view = view('admin.pages.super_admin.required-documents.manage', compact( 'required_documents_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get add services category wise required documents form
    public function getAddRequiredDocument(Request $request)
    {
        $view = view('admin.pages.super_admin.required-documents.form');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //edit required documents
    public function getEditRequiredDocument($id, Request $request)
    {
        $required_document = RequiredDocuments::where('id', $id)->first();
        if ($required_document != Null) {
            $view = view('admin.pages.super_admin.required-documents.form', compact('required_document'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        } else {
            Session::flash('error', 'Required Document Not Found!');
            return redirect()->back();
        }
    }

    //save or update required documents
    public function postUpdateRequiredDocument(RequiredDocumentsRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $id = $request->get('id');
        if ($id != Null) {
            $required_document = RequiredDocuments::query()->where('id', $request->get('id'))->first();
        } else {
            $required_document = new RequiredDocuments();
        }
        $required_document->name = $request->get('name');
        $required_document->status = $request->get('status');
        $required_document->contains_expiry = ($request->get('contains_expiry') == null) ? 0 : $request->get('contains_expiry');
        $required_document->save();

        if ($id != Null) {
            Session::flash('success',' Required Document Updated successfully!');
        } else {
            Session::flash('success',' Required Document Added successfully!');
        }
            return redirect()->route('get:admin:required_document_list');
    }

    //start PromoCode Module
    //Admin PromoCode List
    public function getAdminPromocodeList(Request $request)
    {

        $promocode_list = PromocodeDetails::query()->get();
        $view = view('admin.pages.super_admin.promocode.manage', compact('promocode_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //Admin Change PromoCode Status
    public function getAdminPromocodeChangeStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            //Session::flash('error', 'product Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $promocode_details = PromocodeDetails::query()->where('id', $id)->first();
        if ($promocode_details == Null) {
            //Session::flash('error', 'product Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Promocode detail not found'
            ]);
        }
        if ($promocode_details->status == 1) {
            $promocode_details->status = 0;
            $promocode_details->save();
        } else {
            $promocode_details->status = 1;
            $promocode_details->save();
        }
        return response()->json([
            'success' => true,
            'status' => $promocode_details->status
        ]);
    }

    //Admin Add PromoCode
    public function getAdminAddPromocode(Request $request)
    {
        $view = view('admin.pages.super_admin.promocode.form');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //Admin Store PromoCode
    public function postAdminUpdatePromocode(PromocodeDetailsRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $id = $request->get('id');
        if ($id != Null) {
            Session::flash('success', 'Promo Code Update Successfully!');
            $promocode_details = PromocodeDetails::query()->where('id', $id)->first();
        } else {
            Session::flash('success', 'Promo Code Add Successfully!');
            $promocode_details = new PromocodeDetails();
            $promocode_details->status = 1;
        }
        $promocode_details->promo_code = trim(strtoupper($request->get('code_name')));
        $promocode_details->discount_amount = $request->get('discount_amount');
        $promocode_details->discount_type = $request->get('discount_type');
        $promocode_details->min_order_amount = $request->get('min_order_amount') != Null ? $request->get('min_order_amount') : 0;
        $promocode_details->max_discount_amount = $request->get('max_discount_amount') != Null ? $request->get('max_discount_amount') : 0;
        $promocode_details->coupon_limit = $request->get('coupon_limit') != Null ? $request->get('coupon_limit') : 0;
        $promocode_details->usage_limit = $request->get('usage_limit');
        $promocode_details->expiry_date_time = Date('Y-m-d H:i:s', strtotime($request->get('expiry_date_time')));
        $promocode_details->description = $request->get('description');
        $promocode_details->save();

            return redirect()->route('get:admin:promocode_list');
    }

    //Admin Edit PromoCode
    public function getAdminEditPromocode(Request $request,$id)
    {

        $promocode_details = PromocodeDetails::where('id', $id)->first();
        $view = view('admin.pages.super_admin.promocode.form', compact('promocode_details'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //end PromoCode Module

    public function getAdminWorldCurrencyList(Request $request)
    {
        $currencies = WorldCurrency::query()->get();
        $view = view('admin.pages.super_admin.world_currency.manage', compact('currencies'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminWorldCurrencyList(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $ratios = $request->get('ratio');
        if ($ratios != Null) {
            foreach ($ratios as $key => $ratio) {
                $get_currency = WorldCurrency::query()->where('id', $key)->first();
                if ($get_currency != Null) {
                    $get_currency->ratio = $ratio;
                    $get_currency->save();
                }
            }
        }
        Session::flash('success', 'Currencies Update Successfully!');
        return redirect()->route('get:admin:world_currency_list');
    }


    //get support pages list
    public function getAdminSupportPages(Request $request)
    {
        $my_checkuout_pages_list = PageSettings::query()->where('type', 1)->get();
        $view = view('admin.pages.super_admin.support_pages.manage', compact('my_checkuout_pages_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get add support pages
    public function getAdminAddPages(Request $request)
    {
        $language_lists = LanguageLists::query()->where('status', '=', '1')->get();
        $view = view('admin.pages.super_admin.support_pages.add_new', compact( 'language_lists'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get edit support pages
    public function getAdminEditPages(Request $request, $page_id)
    {
        $language_lists = LanguageLists::query()->where('status', '=', '1')->get();
        $pages = PageSettings::query()->where('id', '=', $page_id)->first();
        $view = view('admin.pages.super_admin.support_pages.add_new', compact( 'pages', 'language_lists'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //post update support pages
    public function postAdminUpdateSupportPages(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $pages = PageSettings::where('id', $request->get('id'))->first();
        if ($pages == Null) {
            $pages = new PageSettings();
        }
        $pages->name = $request->get('name');
        $pages->description = $request->get('description');

        try {
            $language_list = LanguageLists::query()->select('language_name as name',
                DB::raw("(CASE WHEN language_code != 'en' THEN  concat(language_code,'_name') ELSE 'name' END) as page_setting_name"),
                DB::raw("(CASE WHEN language_code != 'en' THEN  concat(language_code,'_description') ELSE 'name' END) as page_desc_name")
            )->where('status', 1)->get();
            foreach ($language_list as $key => $language) {
                if (Schema::hasColumn('page_settings', $language->page_setting_name) && Schema::hasColumn('page_settings', $language->page_desc_name)) {
                    $pages->{$language->page_setting_name} = $request->get($language->page_setting_name);
                    $pages->{$language->page_desc_name} = $request->get($language->page_desc_name);
                }

            }

        } catch (\Exception $e) {
        }
        $pages->save();
        if ($request->get('id') == Null) {
            $pages->save();
        }
        return redirect()->route('get:admin:support_pages');

    }

    //delete support pages
    public function getAdminDeleteSupportPages(Request $request)
    {
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Page Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $page = PageSettings::where('id', $id)->first();
        if ($page == Null) {
            Session::flash('error', 'Page Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'ddd not found'
            ]);
        }
        $page->delete();
        Session::flash('success', 'Page remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    //get AdminAbout pages
    public function getAdminAboutPages(Request $request)
    {
        $pages = PageSettings::where('name', 'about-us')->first();
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.support_pages.form', compact('pages'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.support_pages.form', compact('pages'));
    }

    //get support pages
    public function getAdminContactUsPages(Request $request)
    {
        $pages = PageSettings::where('name', 'contact-us')->first();
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.support_pages.form', compact('pages'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.support_pages.form', compact('pages'));
    }

    public function getAdminFaqPages(Request $request)
    {
        $pages = PageSettings::where('name', 'faq')->first();
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.support_pages.form', compact('pages'))->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.support_pages.form', compact('pages'));
    }

    //delete un register provider
    public function getDeleteUnRegisterProvider(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }

        $provider_id = $request->get('provider_id');
        if ($provider_id == Null) {
//            Session::flash('error', 'Provider Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $provider = Provider::where('id', $provider_id)->first();
        if ($provider == Null) {
//            Session::flash('error', 'Provider Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($provider->status == 3) {
            if (\File::exists(public_path('/assets/images/profile-images/provider/' . $provider->avatar))) {
                \File::delete(public_path('/assets/images/profile-images/provider/' . $provider->avatar));
            }

            $provider->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
//        Session::flash('error', 'Provider remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }


    //get push notification
    public function getAdminPushNotification(Request $request)
    {
        $push_notification = PushNotification::select(
            "id", "notification_type", "title", "message", "created_at",
            DB::raw("(CASE
                    WHEN notification_type = '1' THEN 'All Users, Drivers'
                    WHEN notification_type = '3' THEN 'All Drivers'
                    ELSE 'All Users'
                END) as notification_type")
        )->orderBy('id', 'desc')->get();
        PushEventTemplateHelper::syncCatalog();
        $push_event_templates = PushEventTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $view = view('admin.pages.super_admin.push_notification.form_manage', compact('push_notification', 'push_event_templates'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //save or update general settings
    public function postAdminUpdatePushNotification(Request $request)
    {
        info($request->all());
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $general_settings = request()->get('general_settings');
        $push_notification = new PushNotification();
        $push_notification->notification_type = $request->get('notification_type');
        $push_notification->title = $request->get('title');
        $push_notification->message = $request->get('message');
        $push_notification->save();
        if ($request->get('notification_type') == 1) {
            $send = "All Users, Drivers";
            info($send);
            $responseUser = $this->notificationClass->sendPushNotification(topic : $general_settings->fcm_user_topic_name, title : $push_notification->title, message : $push_notification->message, notification_type : 0);
            $responseDriver = $this->notificationClass->sendPushNotification(topic : $general_settings->fcm_driver_topic_name, title : $push_notification->title, message : $push_notification->message, notification_type : 0);

            info("response of :- " . $general_settings->fcm_user_topic_name);
            info($responseUser);

            info("response of :- " . $general_settings->fcm_driver_topic_name);
            info($responseDriver);
        }

        if ($request->get('notification_type') == 2) {
            $send = "All Users";
            info($send);
            info("fcm_user_topic_name");
            info($general_settings->fcm_user_topic_name);
            $res = $this->notificationClass->sendPushNotification(topic : $general_settings->fcm_user_topic_name, title : $push_notification->title, message : $push_notification->message, notification_type : 0);
            info("response");
            info($res);
        }

        if ($request->get('notification_type') == 3) {
            $send = "All Drivers";
            info($send);
            info("fcm_user_topic_name");
            info($general_settings->fcm_driver_topic_name);
            $res = $this->notificationClass->sendPushNotification(topic : $general_settings->fcm_driver_topic_name, title : $push_notification->title, message : $push_notification->message, notification_type : 0);
            info("response");
            info($res);
        }
        return redirect()->route('get:admin:push_notification')->with("success", ucwords($send) . " Sent Notifications Successfully.");
    }

    public function postAdminSavePushEventTemplates(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $rows = $request->get('events', []);
        if (! is_array($rows)) {
            return redirect()->route('get:admin:push_notification')->with('error', 'Invalid event payload.');
        }

        foreach ($rows as $id => $row) {
            if (! is_array($row)) {
                continue;
            }
            $template = PushEventTemplate::query()->where('id', (int) $id)->first();
            if ($template === null) {
                continue;
            }
            $template->title_es = trim((string) ($row['title_es'] ?? $template->title_es));
            $template->message_es = trim((string) ($row['message_es'] ?? $template->message_es));
            $template->title_en = trim((string) ($row['title_en'] ?? ''));
            $template->message_en = trim((string) ($row['message_en'] ?? ''));
            $template->sound_profile = in_array(($row['sound_profile'] ?? 'default'), ['default', 'new_request'], true)
                ? $row['sound_profile']
                : 'default';
            $template->is_active = isset($row['is_active']) ? 1 : 0;
            $template->save();
        }

        PushEventTemplateHelper::clearCache();

        return redirect()->route('get:admin:push_notification')->with('success', 'Event notification templates saved successfully.');
    }

    public function getAdminDeletePushNotification(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Customer Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $notification = PushNotification::where('id', $id)->first();
        if ($notification == Null) {
//            Session::flash('error', 'Customer Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $notification->delete();
//        Session::flash('success', 'Customer remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    //get sub admin list
    public function getAdminSubAdminList(Request $request)
    {
        $sub_admin_list = Admin::query()->where('roles', '4')->get();
        $view = view('admin.pages.super_admin.sub_admin.manage', compact('sub_admin_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //get sub admin add
    public function getAdminAddSubAdmin(Request $request)
    {
        $admin_all_module = AdminModule::query()->select('id', 'name', 'module_name', 'module_action')
            ->where('status', '=', 1)
            ->get();
        foreach ($admin_all_module as $singel_module) {
            $getAllPageAction = AdminPageAction::query()->select('id', 'constant', 'name')->get();
            $module_action_checkbox = [];
            foreach ($getAllPageAction as $singleAction) {
                if (!in_array($singleAction->id, explode(',', $singel_module->module_action))) {
                    continue;
                }
                $module_action_checkbox[] = [
                    'id' => $singleAction->id,
                    'name' => $singleAction->name,
                    'constant' => $singleAction->constant,
                    'checked' => ""
                ];
            }
            $module_with_action[] =
                [
                    'module_id' => $singel_module->id,
                    'name' => $singel_module->name,
                    'module_name' => $singel_module->module_name,
                    'module_action' => $singel_module->module_action,
                    'checkbox' => $module_action_checkbox,
                ];
        }

        $view = view('admin.pages.super_admin.sub_admin.add_new', compact('module_with_action'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminRestrictedAreaList(Request $request)
    {
        $area_list = RestrictedArea::query()->get();
        $view = view('admin.pages.super_admin.geo_fencing.manage', compact('area_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminAddRestrictedArea(Request $request)
    {
        $view = view('admin.pages.super_admin.geo_fencing.form');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminUpdateRestrictedArea(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        if ($request->get('latitude') == Null || $request->get('longitude') == Null || $request->get('area_name') == Null) {
            Session::flash('error', 'Please select restricted ares!');
            return redirect()->back();
        }
        $id = $request->get('id') != Null ? $request->get('id') : 0;
        $area = RestrictedArea::query()->where('id', $id)->first();
        if ($area == Null) {
            $area = new RestrictedArea();
        }
        $area->name = $request->get('area_name');
        $area->latitude = $request->get('latitude');
        $area->longitude = $request->get('longitude');
        //$area->restrict_location = $request->get('restrict_location');
        //$area->restrict_type = $request->get('restrict_type');
        $area->status = $request->get('status');
        $area->save();
        if($id != 0){
            $msg ='Restricted area updated successfully!';
        }else{
            $msg ='Restricted area added successfully!';
        }
        return redirect()->route('get:admin:restricted_area_list')->with('success',$msg);
    }

    public function getAdminEditRestrictedArea(Request $request, $id)
    {
        $area_details = RestrictedArea::query()->where('id', $id)->first();
        info('-------------------area_details-------------------');
        info($area_details);
        $view = view('admin.pages.super_admin.geo_fencing.form', compact('area_details'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminUpdateRestrictedAreaStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $area_details = RestrictedArea::query()->where('id', '=', $id)->first();
        if ($area_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($area_details->status == 1) {
            $status = $area_details->status = 0;
        } else {
            $status = $area_details->status = 1;
        }
        $area_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    public function getAdminDeleteRestrictedArea(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Area Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user = RestrictedArea::where('id', $id)->first();
        if ($user == Null) {
            Session::flash('error', 'Area Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $user->delete();
        Session::flash('success', 'Area remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    public function getEmailTemplatesList(Request $request)
    {
        $email_templates = EmailTemplates::query()->get();
        $view = view('admin.pages.super_admin.email_templates.manage', compact('email_templates'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminAddEmailTemplates(Request $request)
    {
        $view = view('admin.pages.super_admin.email_templates.form');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminEditEmailTemplates(Request $request, $id)
    {
        $email_templates = EmailTemplates::query()->where('id', $id)->first();
        if ($email_templates == Null) {
            return redirect()->back();
        }
        $view = view('admin.pages.super_admin.email_templates.form', compact('email_templates'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postAdminUpdateEmailTemplates(EmailTemplatesRequest $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }
        $id = $request->get('id');
        $email_templates = EmailTemplates::query()->where('id', $id)->first();
        Session::flash('success', 'Template updated successfully!');
        if ($email_templates == Null) {
            Session::flash('success', 'Template added successfully!');
            $email_templates = new EmailTemplates();
        }
        $email_templates->title = $request->get('title');
        $email_templates->content = $request->get('content');
        $email_templates->save();

        return redirect()->route('get:admin:email_templates');
    }

    public function postAdminUpdateEmailTemplatesStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $template_details = EmailTemplates::query()->where('id', '=', $id)->first();
        if ($template_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($template_details->status == 1) {
            $status = $template_details->status = 0;
        } else {
            $status = $template_details->status = 1;
        }
        $template_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    public function getAdminDeleteEmailTemplates(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $id = $request->get('id');
        if ($id == Null) {
            Session::flash('error', 'Area Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $template = EmailTemplates::where('id', $id)->first();
        if ($template == Null) {
            Session::flash('error', 'Area Not Found!');
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $template->delete();
        Session::flash('success', 'Area remove successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    //get Language Lists
    public function getAdminLanguageLists(Request $request)
    {
        $language_lists = LanguageLists::query()->orderBy('id', 'asc')->get();
        $view = view('admin.pages.super_admin.language_lists.form_manage', compact('language_lists'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    //save or update Language Lists
    public function postAdminUpdateLanguageLists(Request $request)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $language_name = $request->get('language_name');
//        $language_code = trim(strtolower(str_replace(" ","",$request->get('language_code'))));
        $language_code = str_replace(" ", "", $request->get('language_code'));

        $get_Exist_code = LanguageLists::query()->where('language_code', $language_code)->first();

        if ($get_Exist_code == Null) {
            //add new column in service category(en_name) ,other_service_sub_category,page_settings,user_package_booking_quantity
            $col_name = $language_code . "_name";
            $page_setting_desc_col = $language_code . "_description";
            $constant_name = $language_code . "_value";
            try {

                //add column at service cteogry
                if (!Schema::hasColumn('vehicle_services', $col_name)) {
                    Schema::table('vehicle_services', function (Blueprint $table) use ($col_name) {
                        $table->string($col_name)->after('name')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                    //add value in new column
                    VehicleService::query()->where('name', '!=', "")
                        ->update([
                            $col_name => DB::raw("name"),
                        ]);
                }


                //add column at page_settings
                if (!Schema::hasColumn('page_settings', $col_name)) {
                    Schema::table('page_settings', function (Blueprint $table) use ($col_name, $page_setting_desc_col) {
                        $table->string($col_name)->after('name')->nullable();
                        $table->longText($page_setting_desc_col)->after('description')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                }

                // add column in language_constant table
                if (!Schema::hasColumn('language_constant', $constant_name)) {
                    Schema::table('language_constant', function (Blueprint $table) use ($constant_name) {
                        $table->string($constant_name)->after('value')->collation('utf8mb4_unicode_ci')->nullable();
                    });
                    //add value in new column
                    LanguageConstant::query()->where('value', '!=', "")
                        ->update([
//                            $constant_name => $language_code . "-" . DB::raw("value"),
                            $constant_name => DB::raw("CONCAT('" . $language_code . "-', value)"),
                        ]);
                }

                //add column at sos
                if (!Schema::hasColumn('sos', $col_name)) {
                    Schema::table('sos', function (Blueprint $table) use ($col_name) {
                        $table->string($col_name,255)->after('name')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                    //add value in new column
                    Sos::query()->where('name', '!=', "")
                        ->update([
                            $col_name => DB::raw("CONCAT('" . $language_code . "-', name)"),
                        ]);
                }

                $language_lists = new LanguageLists();
                $language_lists->language_name = $language_name;
                $language_lists->language_code = $language_code;
                $language_lists->save();
            } catch (\Exception $e) {
//                dd($e->getMessage());
                return redirect()->route('get:admin:language_lists')->with("error", " Language Field is not properly added.");
            }

            return redirect()->route('get:admin:language_lists')->with("success", " Language Added Successfully.");
        } else {


            //add new column in service category(en_name) ,other_service_sub_category,page_settings,user_package_booking_quantity
            $col_name = $language_code . "_name";
            $page_setting_desc_col = $language_code . "_description";
            $constant_name = $language_code . "_value";
            try {

                //add column at service cteogry
                if (!Schema::hasColumn('vehicle_services', $col_name)) {
                    Schema::table('vehicle_services', function (Blueprint $table) use ($col_name) {
                        $table->string($col_name)->after('name')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                    //add value in new column
                    VehicleService::query()->where('name', '!=', "")
                        ->update([
                            $col_name => DB::raw("name"),
                        ]);
                }

                //add column at page_settings
                if (!Schema::hasColumn('page_settings', $col_name)) {
                    Schema::table('page_settings', function (Blueprint $table) use ($col_name, $page_setting_desc_col) {
                        $table->string($col_name)->after('name')->nullable();
                        $table->longText($page_setting_desc_col)->after('description')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                }

                // add column in language_constant table
                if (!Schema::hasColumn('language_constant', $constant_name)) {
                    Schema::table('language_constant', function (Blueprint $table) use ($constant_name) {
                        $table->string($constant_name)->after('value')->collation('utf8mb4_unicode_ci')->nullable();
                    });
                    //add value in new column
                    LanguageConstant::query()->where('value', '!=', "")
                        ->update([
//                            $constant_name => $language_code . "-" . DB::raw("value"),
                            $constant_name => DB::raw("CONCAT('" . $language_code . "-', value)"),
                        ]);
                }

                //add column at sos
                if (!Schema::hasColumn('sos', $col_name)) {
                    Schema::table('sos', function (Blueprint $table) use ($col_name) {
                        $table->string($col_name,255)->after('name')->collation('utf8mb4_unicode_ci')->nullable();
                    });

                    //add value in new column
                    Sos::query()->where('name', '!=', "")
                        ->update([
                            $col_name => DB::raw("CONCAT('" . $language_code . "-', name)"),
                        ]);
                }

            } catch (\Exception $e) {
                return redirect()->route('get:admin:language_lists')->with("error", " Language Field is not properly added.");
            }

            return redirect()->route('get:admin:language_lists')->with("error", " Language Code Already Added.");
        }

    }

    public function getAdminUpdateLanguageLists(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $lang_details = LanguageLists::query()->where('id', '=', $id)->first();

        if ($lang_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($lang_details->status == 1) {
            $status = $lang_details->status = 0;
        } else {
            $status = $lang_details->status = 1;
        }
        $lang_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }


    //get constant lists
    public function getAdminLanguageConstant(Request $request)
    {
        $language_constant = LanguageConstant::query()->orderBy('id', 'asc')->get();
        $language_lists = LanguageLists::query()->orderBy('id', 'asc')->get();
        $view = view('admin.pages.super_admin.language_constant.form_manage', compact('language_constant', 'language_lists'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function getAdminEditLanguageConstant(Request $request, $id = 0)
    {
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $language_single_constant = LanguageConstant::query()->where('id', '=', $id)->first();
        $language_constant = LanguageConstant::query()->orderBy('id', 'asc')->get();

        $language_lists = LanguageLists::query()->orderBy('id', 'asc')->get();
        if ($language_single_constant != Null) {
            $view = view('admin.pages.super_admin.language_constant.form_manage', compact('language_single_constant', 'language_constant', 'language_lists'));
            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }
            return $view;
        } else {
            return redirect()->route('get:admin:language_constant')->with("error", " Language Constant value is not properly edited.");
        }

    }

    public function postAdminUpdateLanguageConstant(Request $request)
    {

        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        $constant_name = $request->get('constant_name');
        $constant_id = $request->get('id');
        $value = $request->get('value');
        if ($constant_id > 0) {
            $get_language_constant = LanguageConstant::query()->where('id', $constant_id)->first();
        } else {
            $get_language_constant = new LanguageConstant();
        }

        try {
            $language_list = LanguageLists::query()->select('language_name as name',
                DB::raw("(CASE WHEN language_code != 'en' THEN  concat(language_code,'_value') ELSE 'name' END) as constant_val")
            )->where('status', 1)->get();
            foreach ($language_list as $key => $language) {
                if (Schema::hasColumn('language_constant', $language->constant_val)) {
                    $get_language_constant->{$language->constant_val} = $request->get($language->constant_val);
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('get:admin:language_constant')->with("error", " Language Constant value is not properly added.");
        }

        $get_language_constant->constant_name = strtoupper(str_replace(" ", "_", $constant_name));
        $get_language_constant->value = $value;
        $get_language_constant->save();

        return redirect()->route('get:admin:language_constant')->with("success", " Language Constant Added Successfully.");

    }

    //change in admin can change user password
    public function getUpdateUserChangePassword(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $validator = Validator::make($request->all(), [
                "user_id" => "required|numeric",
                "password" => "required",
                "confirm_password" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $user_id = $request->get('user_id');
        $users = User::query()->select('id')
            ->where('id', $user_id)->first();
        if ($users == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $user = User::query()->where('id', '=', $users->id)->first();
        if ($user == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Password Successfully Changed'
        ]);
    }


    //change in admin can change driver and provider password
    public function getUpdateProviderChangePassword(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $validator = Validator::make($request->all(), [
                "provider_id" => "required|numeric",
                "password" => "required|min:6",
                "confirm_password" => "required|same:password",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                "message" => $validator->errors()->first(),
            ]);
        }
        $provider_data = Provider::query()->select('providers.id')
            ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
            ->where('provider_services.id', $request->get('provider_id'))->whereNull('providers.deleted_at')->first();
        if ($provider_data == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $provider = Provider::query()->where('id', $provider_data->id)->first();
        if ($provider == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $provider->password = Hash::make($request->get('password'));
        $provider->save();
        return response()->json([
            'success' => true,
            'message' => 'Password Successfully Changed'
        ]);
    }

    public function postAdminProviderWalletTransaction(Request $request, $id)
    {
        if (!is_numeric($id) || $id == Null) {
            Session::flash('error', 'Provider wallet transaction Not found!');
            return redirect()->back();
        }
        $user_details = User::select('id', 'first_name', 'email', 'contact_number', 'status')
            ->where('id', $id)
            ->first();
        if ($user_details == Null) {
            Session::flash('error', 'Provider wallet transaction Not found!');
            return redirect()->back();
        }

        $wallet_transaction_list = UserWalletTransaction::select(
            'id', 'amount', 'subject', 'remaining_balance', 'created_at',
            DB::raw("(CASE WHEN transaction_type = 1 THEN 'Credit' ELSE (CASE WHEN transaction_type = 2 THEN 'Debit' ELSE '----' END) END) as transaction_type")
        )->where('user_id', $id)->orderBy('id', 'desc')->get();

        $view = view('admin.pages.super_admin.user.wallet_transaction', compact('user_details', 'wallet_transaction_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }
    public function postAdminUpdateProviderWalletTransaction(Request $request)
    {
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        $validator = Validator::make($request->all(), [
                "provider_id" => "required|numeric",
                "wallet_amount" => "required",
                "choose_option" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }
        $provider_id = $request->get('provider_id');
        $provider = User::query()->select('id','currency')
            ->where('id', $provider_id)->first();
        if ($provider == Null) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong'
            ]);
        }

        $amount_to_default = round($request->get('wallet_amount'), 2);
        //get wallet balance
        $last_amount = $this->notificationClass->getWalletBalance($provider_id);

        if ($request->get('choose_option') == 1) {
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $provider_id;
            $add_balance->wallet_provider_type = 0;
            $add_balance->transaction_type = 1;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject_code = 6;
            $add_balance->subject = "credit by Admin";
            $add_balance->remaining_balance = floatval($last_amount + $amount_to_default);
            $add_balance->save();
        } else {
            $add_balance = new UserWalletTransaction();
            $add_balance->user_id = $provider_id;
            $add_balance->wallet_provider_type = 0;
            $add_balance->transaction_type = 2;
            $add_balance->amount = $amount_to_default;
            $add_balance->subject_code = 13;
            $add_balance->subject = "debit by Admin";
            $add_balance->remaining_balance = floatval($last_amount - $amount_to_default);
            $add_balance->save();
        }

        $last_amount = $add_balance->remaining_balance;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'user_id' => $provider->id,
            'last_amount' => $last_amount
        ]);
    }

    public function getReferralList(Request $request)
    {
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.referral.manage')->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.referral.manage');
    }

    public function getReferralListNew(Request $request)
    {
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


        $records = UserReferHistory::query()->select('user_refer_history.*','u1.first_name as user_name','u2.first_name as refer_user_name')
            ->join('users as u1','u1.id','=','user_refer_history.user_id')
            ->join('users as u2','u2.id','=','user_refer_history.refer_id');
        /* ----------------------------------------- Access from City Admin ------------------------------------------*/
        if (request()->get("admin_role") == 4) {
            $admin_city_id = request()->get("admin_city_id");
            $records->where(function ($query) use ($admin_city_id) {
                $query->where('u1.area_id', $admin_city_id)
                    ->orWhere('u2.area_id', $admin_city_id);
            });
        }
        /* ----------------------------------------- End Access from City Admin --------------------------------------*/

        $records = $records->orderBy($columnName, $columnSortOrder);
        /* ----------------------------------------- Searching Filter ------------------------------------------------*/
        if ($searchValue != "") {
            $records = $records->where(function($records) use ($searchValue){
                $records->where('u1.first_name', 'like', '%' . $searchValue . '%');
                $records->orWhere('u2.first_name', 'like', '%' . $searchValue . '%');
                $records->orWhere('user_refer_history.refer_discount', 'like', '%' . $searchValue . '%');
                $records->orWhere('user_refer_history.user_discount', 'like', '%' . $searchValue . '%');
            });
        }
        /* ----------------------------------------- End Searching Filter --------------------------------------------*/
        /* filtered from {{ $totalRecords }} total entries */
        $totalRecords = $records->count();
        /* Showing 1 to 25 of {{ $totalRecordswithFilter }} entries */
        $totalRecordswithFilter = $records->count();

        $records = $records
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = array();

        foreach ($records as $key => $record) {
            $id = $key + 1 + $start;
            $user_discount_type = $record->user_discount_type == 1 ? "Amount" : "Percentage (%)";
            $refer_discount_type = $record->refer_discount_type == 1 ? "Amount" : "Percentage (%)";

            $user_status = $record->user_status == 0 ? '<span class="pending">Pending</span>' : '<span class="claimed">Claimed</span>';
            $refer_status = $record->refer_status == 0 ? '<span class="pending">Pending</span>' : '<span class="claimed">Claimed</span>';

            $data_arr[] = array(
                "id" => $id,
                "user_name" => $record->user_name,
                "user_discount_type" => $user_discount_type,
                "user_discount" => $record->user_discount,
                "user_status" => $user_status,
                'refer_user_name' => $record->refer_user_name,
                'refer_discount_type' => $refer_discount_type,
                'refer_discount' => $record->refer_discount,
                'refer_status' => $refer_status,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return json_encode($response);
    }

    public function getReferredList(Request $request,$id)
    {
        $user = User::query()->where('id',$id)->withTrashed()->first();

        if($user == NULL){
            return redirect()->back()->with('error','No User Found !');
        }
        $user_id = $user->id;
        if ($request->ajax()) {
            $view = view('admin.pages.super_admin.user.referral')->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return view('admin.pages.super_admin.user.referral',compact('user_id'));
    }

    public function getReferredListNew(Request $request,$id)
    {
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

        $records = UserReferHistory::query()->select('user_refer_history.id','users.first_name as user_name','user_refer_history.refer_discount_type','user_refer_history.refer_discount','user_refer_history.refer_status')
            ->join('users','users.id','=','user_refer_history.user_id')
            ->where('user_refer_history.refer_id',$id);

        $records = $records->orderBy($columnName, $columnSortOrder);
        /* ----------------------------------------- Searching Filter ------------------------------------------------*/
        if ($searchValue != "") {
            $records = $records->where(function($records) use ($searchValue){
                $records->where('users.first_name', 'like', '%' . $searchValue . '%');
                $records->orWhere('user_refer_history.refer_discount', 'like', '%' . $searchValue . '%');
            });
        }
        /* ----------------------------------------- End Searching Filter --------------------------------------------*/
        /* filtered from {{ $totalRecords }} total entries */
        $totalRecords = $records->count();
        /* Showing 1 to 25 of {{ $totalRecordswithFilter }} entries */
        $totalRecordswithFilter = $records->count();

        $records = $records
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = array();

        foreach ($records as $key => $record) {
            $no = $key + 1 + $start;
            $refer_discount_type = $record->refer_discount_type == 1 ? "Amount" : "Percentage (%)";
            $refer_status = $record->refer_status == 0 ? '<span class="pending">Pending</span>' : '<span class="claimed">Claimed</span>';
            $data_arr[] = array(
                "id" => $no,
                "user_name" => $record->user_name,
                'refer_discount_type' => $refer_discount_type,
                'refer_discount' => $record->refer_discount,
                'refer_status' => $refer_status,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return json_encode($response);
    }

    //Manage sos list
    public function showSos(){
        return view('admin.pages.super_admin.sos.manage');
    }

    public function showSosTriggerLogs()
    {
        return view('admin.pages.super_admin.sos.trigger_logs');
    }

    public function getSosTriggerLogList(Request $request)
    {
        if (! Schema::hasTable('sos_trigger_logs')) {
            return response()->json([
                'draw' => intval($request->get('draw')),
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                'aaData' => [],
            ]);
        }

        $start = $request->get('start');
        $columnIndex = $request->get('order')[0]['column'] ?? 0;
        $columnName = $request->get('columns')[$columnIndex]['data'] ?? 'triggered_at';
        $columnSortOrder = $request->get('order')[0]['dir'] ?? 'desc';
        $searchValue = $request->get('search')['value'] ?? '';

        $baseQuery = SosTriggerLog::query()
            ->leftJoin('users', 'users.id', '=', 'sos_trigger_logs.user_id')
            ->select(
                'sos_trigger_logs.*',
                DB::raw("CONCAT(COALESCE(users.first_name,''), ' ', COALESCE(users.last_name,'')) as user_display_name")
            );

        $totalRecords = SosTriggerLog::query()->count();

        if ($searchValue !== '') {
            $baseQuery->where(function ($query) use ($searchValue) {
                $query->where('sos_trigger_logs.contact_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('sos_trigger_logs.contact_number', 'like', '%' . $searchValue . '%')
                    ->orWhere('sos_trigger_logs.ride_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.last_name', 'like', '%' . $searchValue . '%');
            });
        }

        $iTotalDisplayRecords = (clone $baseQuery)->count();

        $sortable = [
            'id' => 'sos_trigger_logs.id',
            'triggered_at' => 'sos_trigger_logs.triggered_at',
            'ride_id' => 'sos_trigger_logs.ride_id',
            'user_role' => 'sos_trigger_logs.user_role',
        ];
        $orderColumn = $sortable[$columnName] ?? 'sos_trigger_logs.triggered_at';

        $records = $baseQuery
            ->orderBy($orderColumn, $columnSortOrder)
            ->skip($start)
            ->take($request->get('length'))
            ->get();

        $data_arr = [];
        foreach ($records as $record) {
            $temp = ++$start;
            $userLabel = trim((string) ($record->user_display_name ?? ''));
            if ($userLabel === '') {
                $userLabel = 'User #' . $record->user_id;
            }
            $data_arr[] = [
                'id' => $temp,
                'triggered_at' => $record->triggered_at?->format('Y-m-d H:i:s') ?? '',
                'user' => $userLabel . ' (' . $record->user_id . ')',
                'user_role' => ucfirst((string) $record->user_role),
                'ride_id' => $record->ride_id ?? '—',
                'contact' => trim(($record->country_code ?? '') . ' ' . ($record->contact_number ?? '')),
                'contact_name' => $record->contact_name ?? '—',
                'location' => ($record->latitude && $record->longitude)
                    ? round((float) $record->latitude, 5) . ', ' . round((float) $record->longitude, 5)
                    : '—',
            ];
        }

        return response()->json([
            'draw' => intval($request->get('draw')),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $iTotalDisplayRecords,
            'aaData' => $data_arr,
        ]);
    }

    //Fetch sos list for datatable through ajax
    public function getSosList(Request $request){
        // Get the starting record number for pagination
        $start = $request->get("start");
        // Determine the index of the column to be sorted, based on the ordering array
        $columnIndex = $request->get('order')[0]['column'];
        // Use the column index to find the actual column name to be used for ordering
        $columnName = $request->get('columns')[$columnIndex]['data'];
        // Get the sorting direction (asc or desc) from the order array
        $columnSortOrder = $request->get('order')[0]['dir'];
        // Extract the search value, which will be used to filter the data
        $searchValue = $request->get('search')['value'];

        $records = Sos::query()->select('sos.*');

        $totalRecords = $records->count();
        if ($searchValue != null) {
            $records->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
                $query->orWhere('contact_number', 'like', '%' . $searchValue . '%');
            });
        }
        $iTotalDisplayRecords = $records->count();

        $records = $records
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($request->get("length"))
            ->get();

        $data_arr = array();
        foreach ($records as $record) {
            $temp = ++$start;
            $data_arr[] = array(
                'id' => $temp,
                'sos_id' => $record->id,
                'name' => $record->name,
                'contact_number' => User::ContactNumber2Stars($record->country_code . $record->contact_number),
                'status' => '<span class="toggle"><label><input name="status" class="form-control sos_status" id="sos_list_'.$record->id.'" sos_list_id="'.$record->id.'" sos_status="'.$record->status.'" type="checkbox" '. (("1" == $record->status) ? 'checked' : '' ).'>
                 <span class="button-indecator" data-toggle="tooltip" data-placement="top" id="title_status_'.$record->id.'"
                 title="'.(("1" == $record->status) ? 'Active' : 'InActive') .'"></span></label></span>',
                'action' => '<a class="render_link" href="'. route('get:admin:edit_sos',[$record->id]) .'"><img src="'. asset('/assets/images/template-images/writing-1.png') .'" style="width:20px; height: 20px;" data-toggle="tooltip" data-placement="top" title="Edit"></a> <a class="delete" sosid="'.$record->id.'"><img src="'. asset('/assets/images/template-images/remove-1.png') .'" style="width:20px; height: 20px;" data-toggle="tooltip" data-placement="top" title="Delete"></a>',
            );
        }

        return response()->json([
            // Get the draw count from the request, which is used by DataTables for pagination and ordering
            "draw" => intval($request->get('draw')),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $iTotalDisplayRecords,
            "aaData" => $data_arr,
        ]);
    }

    //Add Sos form
    public function addSos(){
        $language_lists = LanguageLists::query()->orderBy('id', 'asc')->get();
        return view('admin.pages.super_admin.sos.form', compact('language_lists'));
    }

    //Edit Sos form
    public function editSos($id = null){
        if (!is_numeric($id) || $id == Null) {
            Session::flash('error', 'Data Not found!');
            return redirect()->back();
        }
        $sos = Sos::query()->where('id', '=', $id)->first();
        $language_lists = LanguageLists::query()->orderBy('id', 'asc')->get();

        if ($sos == Null) {
            $view = view('admin.pages.super_admin.sos.form',compact('language_lists'));
        } else {
            $view = view('admin.pages.super_admin.sos.form', compact('sos','language_lists'));
        }
        return $view;
    }

    //Add or update Sos record
    public function saveUpdateSos(SosRequest $request){
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        // Retrieve the Sos record by ID or create a new instance if not found
        $sos = Sos::query()
            ->where('id', $request->get('id'))
            ->first();

        if($sos == null){
            // If no record exists, create a new one and set a success message
            $sos = new Sos();
            Session::flash('success', 'SOS contact added successfully!');
        }else{
            // If a record exists, set a success message for update
            Session::flash('success', 'SOS contact updated successfully!');
        }

        // Format the name input and assign it to the Sos record
        $sos->name = ucwords(strtolower(trim($request->get('name'))));
        try {
            // Retrieve the list of languages with their corresponding column names
            $language_list = LanguageLists::query()->select('language_name as name',
                DB::raw("(CASE WHEN language_code != 'en' THEN  concat(language_code,'_name') ELSE 'name' END) as constant_val")
            )->where('status', 1)->get();
            foreach ($language_list as $key => $language) {
                if (Schema::hasColumn('sos', $language->constant_val)) {
                    $sos->{$language->constant_val} = $request->get($language->constant_val);
                }
            }
        } catch (\Exception $e) {
            // Catch any exceptions during language field assignment
            Session::flash('error', 'SOS contact details not found!');
            return redirect()->back();
        }

        // Assign the status, defaulting to 0 if not provided
        $sos->status = ($request->get('sos_status') == null) ? 0 : $request->get('sos_status');
        $sos->contact_number = $request->get('contact_number');
        $sos->country_code = $request->get('country_code');
        $sos->save();
        // Redirect to the Sos listing route
        return  redirect()->route('get:admin:sos');
    }

    //Delete Sos record
    public function getDeleteSos(Request $request){
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }

        if ($request->get('id') == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        $sos = Sos::where('id', $request->get('id'))->first();
        if ($sos == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        $sos->delete();
        Session::flash('success', 'SOS contact removed successfully!');
        return response()->json([
            'success' => true
        ]);
    }

    //Update Sos status via ajax call
    public function updateSosStatus(Request $request){
        if ($this->is_restricted == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.'
            ]);
        }
        // Validate that an ID is provided in the request.
        if ($request->get('id') == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        // Attempt to find the Sos record by the provided ID.
        $sos = Sos::query()->where('id', '=', $request->get('id'))->first();
        // Check if the record exists.
        if ($sos == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        // Toggle the `status` field: if it's 1, set it to 0; otherwise, set it to 1.
        $sos->status = ($sos->status == 1) ? 0 : 1;

        $sos->save();
        // Respond with the updated status and success message.
        return response()->json([
            'success' => true,
            'status' => $sos->status,
        ]);
    }

    public function getAdminSearchRadius(Request $request)
    {

        $search_radius=SearchRadius::query()
            ->orderBy('radius')
            ->get();
        $view = view('admin.pages.super_admin.search_radius.search_radius_list', compact('search_radius'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    public function postUpdateSearchRadius(Request $request)
    {
        if (
            $request->get('radius') === null ||
            !is_numeric($request->get('radius')) ||
            $request->get('radius') <= 0
        ) {
            Session::flash('error', 'Radius must be greater than 0.');
            return redirect()->back()->withInput();
        }

        $providerSearchRadius = ServiceSettings::query()
            ->value('provider_search_radius'); // get the limit

        if ($request->get('radius') > $providerSearchRadius) {
            Session::flash('error', 'Radius must be within provider search radius limit.');
            return redirect()->back()->withInput();
        }

        $check_service_radius = SearchRadius::query()->where('radius', $request->get('radius'));
        if ($request->get('radius_id') != Null) {
            $check_service_radius=$check_service_radius->where('id', '!=',$request->get('radius_id'));
        }
        $check_service_radius=$check_service_radius->first();
        if ($check_service_radius != Null) {
            Session::flash('error', 'Same Search Radius  already exists!');
            return redirect()->back();
        }

        if ($request->get('radius_id') != Null) {
            $search_radius = SearchRadius::query()->where('id', $request->get('radius_id'))->first();
            Session::flash('success', 'Search Radius updated successfully!');
        } else {
            $search_radius = new SearchRadius();
            Session::flash('success', 'Search Radius added successfully!');
        }
        $search_radius->radius = $request->get('radius');
        $search_radius->save();
        return redirect()->route('get:admin:search_radius_list');
    }

    public function postDeleteSearchRadius(Request $request)
    {
        if ($request->get('id') == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Search Radius not found'
            ]);
        }
        $search_radius = SearchRadius::query()->where('id', $request->get('id'))->first();
        if ($search_radius == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Search Radius not found'
            ]);
        }
        $search_radius->delete();
        return response()->json([
            'success' => true
        ]);
    }

    /*===============================Start City Area List Code====================================*/

    /*---------------------------City AreaList Manage Code-----------------------------------------*/
    public function getAdminCityAreaList(Request $request)
    {
        $view = view('admin.pages.super_admin.city_area.manage');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    /*---------------------------Add City Area Code-----------------------------------------------*/
    public function getAdminAddCityArea(Request $request)
    {
        $view = view('admin.pages.super_admin.city_area.form');
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    /*---------------------------Update City Area Code--------------------------------------------*/
    public function postAdminUpdateCityArea(Request $request)
    {
        // Restrict action if in demo mode
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        // Validate required fields: latitude, longitude, and area name
        if (!$request->filled(['latitude', 'longitude', 'area_name'])) {
            Session::flash('error', 'Please select city areas!');
            return redirect()->back();
        }
        // Get ID from request, default to 0 if not present
        $id = $request->get('id', 0);

        // Try to find the existing area by ID
        $area = AdminAreaList::query()->where('id', $id)->first();
        // Initialize message and flag for new record
        $is_new = 0;
        $message = 'City area Updated successfully!';

        if ($area == Null) {
            $area = new AdminAreaList();
            $message = 'City area added successfully!';
            $vertices_x = explode(",", $request->get('latitude'));
            $vertices_x[] = $vertices_x[0];

            $latitude = implode(",", $vertices_x);

            $vertices_y = explode(",", $request->get('longitude'));
            $vertices_y[] = $vertices_y[0];
            $longitude = implode(",", $vertices_y);
            $is_new = 1;
        } else {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
        }
        $area->name = $request->get('area_name');
        $area->latitude = $latitude;
        $area->longitude = $longitude;
        $area->status = $request->get('status');
        $area->save();
        return redirect()->route('get:admin:city_area_list')->with('success', $message);
    }

    /*---------------------------Edit City Area Code----------------------------------------------*/
    public function getAdminEditCityArea(Request $request, $id)
    {
        $area_details = AdminAreaList::query()->where('id', $id)->first();
        $view = view('admin.pages.super_admin.city_area.form', compact('area_details'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

    /*---------------------------Update City Area Status Code------------------------------------*/
    public function postAdminUpdateCityAreaStatus(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $area_details = AdminAreaList::query()->where('id', '=', $id)->first();
        if ($area_details == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        if ($area_details->status == 1) {
            $status = $area_details->status = 0;
        } else {
            $status = $area_details->status = 1;
        }
        $area_details->save();
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    /*---------------------------Delete City Area Code-------------------------------------------*/
    public function getAdminDeleteCityArea(Request $request)
    {
        if ($this->is_restricted == 1) {
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
        $admin_area_list = AdminAreaList::query()->where('id', $id)->first();
        if ($admin_area_list == Null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Delete feature are not available in this module'
        ]);
//        return response()->json([
//            'success' => true
//        ]);
    }

    /*---------------------------Fetch City Area List with AJAX Code------------------------------*/
    public function getAjaxAdminCityAreaList(Request $request){
        $start = $request->get("start"); // Get the starting record number for pagination
        // Base query for area list
        $query = AdminAreaList::query()->select('id', 'name', 'status');
        // Apply search filter if provided
        if ($searchValue = $request->get('search')['value']) {
            $query->where('name', 'like', "%$searchValue%");
        }
        // Total records count after filtering
        $totalRecordsWithFilter = $query->count();

        // Apply ordering
        $orderColumn = $request->get('order')[0]['column'];
        $orderDir = $request->get('order')[0]['dir'];
        $columns = ['id', 'name', 'status']; // Ensure the columns are in the correct order
        if (isset($columns[$orderColumn])) {
            $column = $columns[$orderColumn];
            $query->orderBy($column, $orderDir);
        }

        // Paginated data fetch
        $areaData = $query->skip($start)->take($request->get("length"))->get();

        // Format data for DataTables
        $areaList = [];
        foreach ($areaData as $key => $area) {
            $areaList[] = [
                "id" => $area->id,
                "no" => $start + $key + 1, // Row number
                "name" => $area->name,
                "status" => $area->status,
            ];
        }

        // Return data in DataTable's expected format
        return response()->json([
            "draw" => intval($request->get('draw')),
            "recordsTotal" => AdminAreaList::count(),
            "recordsFiltered" => $totalRecordsWithFilter,
            "data" => $areaList,
        ]);
    }

    /*---------------------------------------------------------
    | Fetch City Admin List
    |----------------------------------------------------------
    */
    public function getAdminCityAdminList(Request $request){
        $sub_admin_list = Admin::query()->where('roles', '4')->get();
        $view = view('admin.pages.super_admin.city_admin.manage', compact('sub_admin_list'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

     /*---------------------------------------------------------
     | Show Add City Admin Form
     |----------------------------------------------------------
     | This method prepares the data required to display the
     | "Add City Admin" form.
    */

    public function getAdminAddCityAdmin(Request $request){
        // Get already assigned areas
        $exist_admin_city = Admin::query()
            ->where('area_id', "!=", 0)
            ->get()
            ->pluck("area_id")
            ->toArray();

        // Available area list (only active, not already assigned)
        $area_list = AdminAreaList::query()
            ->whereNotIn("id", $exist_admin_city)
            ->where("status", "=", 1)
            ->get();

        // Fetch all parent modules
        $admin_all_module = AdminModule::query()
            ->select('id', 'name', 'module_name', 'module_action', 'module_category_type')
            ->where('parent_id', '=', 0)
            ->where('status', '=', 1)
            ->where('is_access', '=', 1)
            ->orderBy("seq")
            ->get();

        $module_with_action = [];

        if ($admin_all_module->isNotEmpty()) {
            foreach ($admin_all_module as $menu_detail) {
                // Fetch sub-modules (if any)
                $sub_menu_list = AdminModule::query()
                    ->select('id', 'name', 'module_name', 'module_action')
                    ->where('parent_id', '=', $menu_detail->id)
                    ->where('status', '=', 1)
                    ->where('is_access', '=', 1)
                    ->orderBy("seq")
                    ->get();

                $is_checkbox = 1; // By default module has checkboxes
                $sub_module_with_action = [];

                // Handle sub-modules
                if ($sub_menu_list->isNotEmpty()) {
                    $is_checkbox = 0; // If sub-modules exist → the parent has no direct checkbox
                    foreach ($sub_menu_list as $sub_menu_detail) {
                        // Build action checkbox for sub-modules
                        $sub_module_action_checkbox = [];

                        $getAllPageAction = AdminPageAction::query()
                            ->select('id', 'constant', 'name')
                            ->get();

                        foreach ($getAllPageAction as $singleAction) {
                            if (!in_array($singleAction->id, explode(',', $menu_detail->module_action))) {
                                continue;
                            }

                            $sub_module_action_checkbox[] = [
                                'id' => $singleAction->id,
                                'name' => $singleAction->name,
                                'constant' => $singleAction->constant,
                                'checked' => ""
                            ];
                        }

                        $sub_module_with_action[] = [
                            'module_id' => $sub_menu_detail->id,
                            'name' => $sub_menu_detail->name,
                            'module_name' => $sub_menu_detail->module_name,
                            'module_action' => $sub_menu_detail->module_action,
                            'checkbox' => $sub_module_action_checkbox,
                            'is_checkbox_show' => 1,
                            'sub_module_with_action' => [],
                            'menu_category_wise_list' => [],
                            //'checkbox' => $module_action_checkbox,
                        ];
                    }
                }

                // Handle direct module checkboxes OR category-wise lists
                $module_action_checkbox = [];
                $menu_category_wise_list = [];
                if ($is_checkbox == 1) {
                    // Case: normal module actions
                    $getAllPageAction = AdminPageAction::query()->select('id', 'constant', 'name')->get();
                    foreach ($getAllPageAction as $singleAction) {
                        if (!in_array($singleAction->id, explode(',', $menu_detail->module_action))) {
                            continue;
                        }

                        $module_action_checkbox[] = [
                            'id' => $singleAction->id,
                            'name' => $singleAction->name,
                            'constant' => $singleAction->constant,
                            'checked' => ""
                        ];
                    }
                }

                // Final push module data
                $module_with_action[] = [
                    'module_id' => $menu_detail->id,
                    'name' => $menu_detail->name,
                    'module_name' => $menu_detail->module_name,
                    'module_action' => $menu_detail->module_action,
                    'checkbox' => $module_action_checkbox,
                    'is_checkbox_show' => $is_checkbox,
                    'sub_module_with_action' => $sub_module_with_action,
                    'menu_category_wise_list' => $menu_category_wise_list,
                    //'checkbox' => $module_action_checkbox,
                ];
            }
        }

        $on_click_res_module = json_encode([1, 2, 4]);
        $res_module = 0;
        $view = view('admin.pages.super_admin.city_admin.form', compact('module_with_action', 'area_list', 'on_click_res_module', 'res_module'));
        if ($request->ajax()) {
            $view = $view->renderSections();
            return $this->adminClass->renderingResponce($view);
        }
        return $view;
    }

     /*---------------------------------------------------------
     | Edit City Admin
     |---------------------------------------------------------
     */
    public function getAdminEditCityAdmin(Request $request, $admin_id){
        // Fetch admin user by ID
        $admin_user = Admin::query()->where('id', $admin_id)->first();

        if ($admin_user != Null) {
            // Get existing admin city areas except the current one
            $exist_admin_city = Admin::query()
                ->where('area_id', "!=", $admin_user->area_id)
                ->where('area_id', "!=", 0)
                ->get()
                ->pluck("area_id")
                ->toArray();

            // Fetch the available area list (not already assigned)
            $area_list = AdminAreaList::query()
                ->whereNotIn("id", $exist_admin_city)
                ->where("status", "=", 1)
                ->get();

            // Fetch all main admin modules
            $admin_all_module = AdminModule::query()
                ->select('id', 'name', 'module_name', 'module_action', 'module_category_type')
                ->where('parent_id', '=', 0)
                ->where('status', '=', 1)
                ->where('is_access', '=', 1)
                ->orderBy("seq")
                ->get();

            $module_with_action = [];

            if ($admin_all_module->isNotEmpty()) {
                foreach ($admin_all_module as $menu_detail) {
                    // Fetch sub-modules for the current parent module
                    $sub_menu_list = $admin_all_module = AdminModule::query()
                        ->select('id', 'name', 'module_name', 'module_action')
                        ->where('parent_id', '=', $menu_detail->id)
                        ->where('status', '=', 1)
                        ->where('is_access', '=', 1)
                        ->orderBy("seq")
                        ->get();

                    $is_checkbox = 1;
                    $sub_module_with_action = [];

                    /*---------------------------------------------------------
                    | Handle Sub-Modules
                    |---------------------------------------------------------*/
                    if ($sub_menu_list->isNotEmpty()) {
                        $is_checkbox = 0;
                        foreach ($sub_menu_list as $sub_menu_detail) {
                            $sub_module_action_checkbox = [];

                            // Fetch all available page actions
                            $getAllPageAction = AdminPageAction::query()
                                ->select('id', 'constant', 'name')
                                ->get();

                            // Match actions with sub-module
                            foreach ($getAllPageAction as $singleAction) {
                                if (!in_array($singleAction->id, explode(',', $sub_menu_detail->module_action))) {
                                    continue;
                                }

                                // Check if admin already has permission
                                $checkadminPermission = AdminPermission::query()
                                    ->where('admin_id', '=', $admin_id)
                                    ->where('module_id', '=', $sub_menu_detail->id)
                                    ->whereRaw("find_in_set('$singleAction->id',permission)")
                                    ->first();
                                $checked = ($checkadminPermission != NULL) ? "checked" : "";

                                // Store action checkbox info
                                $sub_module_action_checkbox[] = [
                                    'id' => $singleAction->id,
                                    'name' => $singleAction->name,
                                    'constant' => $singleAction->constant,
                                    'checked' => $checked
                                ];
                            }

                            // Append sub-module with actions
                            $sub_module_with_action[] = [
                                'module_id' => $sub_menu_detail->id,
                                'name' => $sub_menu_detail->name,
                                'module_name' => $sub_menu_detail->module_name,
                                'module_action' => $sub_menu_detail->module_action,
                                'checkbox' => $sub_module_action_checkbox,
                                'is_checkbox_show' => 1,
                                'sub_module_with_action' => [],
                                //'checkbox' => $module_action_checkbox,
                            ];
                        }
                    }

                      /*---------------------------------------------------------
                      | Handle Parent Module Checkbox & Category Wise List
                      |---------------------------------------------------------*/
                    $module_action_checkbox = [];
                    $menu_category_wise_list = [];

                    if ($is_checkbox == 1) {
                        // Normal module action checkboxes
                        $getAllPageAction = AdminPageAction::query()
                            ->select('id', 'constant', 'name')
                            ->get();

                        foreach ($getAllPageAction as $singleAction) {
                            if (!in_array($singleAction->id, explode(',', $menu_detail->module_action))) {
                                continue;
                            }

                            $checkadminPermission = AdminPermission::query()
                                ->where('admin_id', '=', $admin_id)
                                ->where('module_id', '=', $menu_detail->id)
                                ->whereRaw("find_in_set('$singleAction->id',permission)")
                                ->first();

                            $checked = ($checkadminPermission != NULL) ? "checked" : "";

                            $module_action_checkbox[] = [
                                'id' => $singleAction->id,
                                'name' => $singleAction->name,
                                'constant' => $singleAction->constant,
                                'checked' => $checked
                            ];
                        }
                    }

                    // Append the parent module with actions & sub-modules
                    $module_with_action[] = [
                        'module_id' => $menu_detail->id,
                        'name' => $menu_detail->name,
                        'module_name' => $menu_detail->module_name,
                        'module_action' => $menu_detail->module_action,
                        'checkbox' => $module_action_checkbox,
                        'is_checkbox_show' => $is_checkbox,
                        'sub_module_with_action' => $sub_module_with_action,
                        'menu_category_wise_list' => $menu_category_wise_list,
                    ];
                }
            }

            $on_click_res_module = json_encode([1, 2, 4]);
            $res_module = 0;
            $view = view('admin.pages.super_admin.city_admin.form', compact('admin_user', 'module_with_action', 'area_list', 'on_click_res_module', 'res_module'));

            if ($request->ajax()) {
                $view = $view->renderSections();
                return $this->adminClass->renderingResponce($view);
            }

            return $view;
        } else {
            // If admin user not found
            Session::flash('error', 'Something want to wrong!');
            return redirect()->back();
        }
    }

    /*---------------------------------------------------------
     | Add / Update City Admin
     |----------------------------------------------------------
     | This function handles creating or updating a City Admin.
     | - Restriction applied in demo mode (no add/edit/delete).
     *---------------------------------------------------------*/

    public function postAdminUpdateCityAdmin(SubAdminRequest $request){
        // Restriction check for demo environment
        if ($this->is_restricted == 1) {
            Session::flash('error', 'Add / Edit / Delete Property has been disabled in the Demo Admin Panel. We will provide the enabled features in the main clone script.');
            return redirect()->back();
        }

        // Ensure at least one module or category permission is selected
        if ($request->get('admin_cat_permission') == Null && $request->get('admin_permission') == Null) {
            Session::flash('error', 'Select at least one of the module.');
            return redirect()->back();
        }

        $city_id = $request->get('city_id');

        // Check if Admin exists or needs to be created
        $admin = Admin::query()->where('id', $request->get('id'))->first();

        if ($admin == Null) {
            // Check if Admin exists or needs to be created
            $exist_admin = Admin::query()->where('area_id', $city_id)->first();

            if ($exist_admin != "") {
                Session::flash('error', 'admin already assign in this city!');
                return redirect()->back();
            }
            $admin = new Admin();
        } else {
            // Prevent duplicate city admin assignment
            $exist_admin = Admin::query()->where('id', "!=", $admin->id)->where('area_id', $city_id)->first();
            if ($exist_admin != "") {
                Session::flash('error', 'admin already assign in this city!');
                return redirect()->back();
            }
        }

        // Assign City Admin details
        $admin->area_id = $city_id;
        $admin->name = $request->get('name');
        $admin->email = $request->get('email');

        // Update password if provided
        if ($request->get('password') != "") {
            $admin->password = Hash::make($request->get('password'));
        }
        $admin->roles = "4"; // Role 4 = City Admin
        $admin->admin_type = 'g'; // Default type
        $admin->save();

         /*---------------------------------------------------------
          | Update Admin Category Permissions
          | Delete old category permissions before adding new ones
          *---------------------------------------------------------*/
        AdminCategoryPermission::query()->where('admin_id', $admin->id)->delete();

        $admin_cat_permission = $request->get('admin_cat_permission');
        if ((isset($admin_cat_permission)) && count($admin_cat_permission) > 0) {
            foreach ($admin_cat_permission as $mIdkey => $get_module_value) {
                if ($get_module_value != Null) {
                    foreach ($get_module_value as $sIdkey => $value) {
                        $get_parent_id = ServiceCategory::query()->select('id')->where('id', '=', $sIdkey)->first();
                        $add_admin_permission_new = new AdminCategoryPermission();
                        $add_admin_permission_new->service_cat_id = $sIdkey;
                        $add_admin_permission_new->admin_id = $admin->id;
                        $add_admin_permission_new->module_id = $mIdkey;
                        $add_admin_permission_new->permission = implode(',', $value);
                        $add_admin_permission_new->save();
                    }
                }
            }
        }

        /*---------------------------------------------------------
          | Update Admin Module Permissions
          | Delete old module permissions before adding new ones
          *---------------------------------------------------------*/
        AdminPermission::query()->where('admin_id', $admin->id)->delete();

        if ($request->get('admin_permission') != Null) {
            if (count($request->get('admin_permission')) > 0) {
                foreach ($request->get('admin_permission') as $key => $value) {

                    $get_parent_id = AdminModule::query()
                        ->select('parent_id')
                        ->where('id', '=', $key)
                        ->first();

                    // Ensure a parent module is added if a child module is selected
                    if ($get_parent_id->parent_id != 0) {

                        $check_parent_add = AdminPermission::query()
                            ->select('id')
                            ->where('admin_id', '=', $admin->id)
                            ->where('module_id', '=', $get_parent_id->parent_id)
                            ->first();

                        if ($check_parent_add == null) {
                            $add_admin_permission = new AdminPermission();
                            $add_admin_permission->admin_id = $admin->id;
                            $add_admin_permission->module_id = $get_parent_id->parent_id;
                            $add_admin_permission->permission = "1";
                            $add_admin_permission->save();
                        }
                    }

                    // Save new permissions for the selected module
                    $add_admin_permission_new = new AdminPermission();
                    $add_admin_permission_new->admin_id = $admin->id;
                    $add_admin_permission_new->module_id = $key;
                    $add_admin_permission_new->permission = implode(',', $value);
                    $add_admin_permission_new->save();
                }
            }
        }

        /*---------------------------------------------------------
         | Final Response with Success Message
         *---------------------------------------------------------*/
        if ($request->get('id') > 0) {
            Session::flash('success', 'City Admin edited Successfully!');
        } else {
            Session::flash('success', 'City Admin added Successfully!');
        }

        return redirect()->route('get:admin:city_admin_list');
    }

     /*---------------------------------------------------------
     | Delete City Admin
     |----------------------------------------------------------
     | This method handles the deletion request for a City Admin.
     |----------------------------------------------------------*/
    public function getAdminDeleteCityAdmin(Request $request){
        // Check if action is restricted in demo mode
        if ($this->is_restricted == 1) {
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

        // Check if City Admin exists
        $admin = Admin::query()->where('id', $id)->first();

        if ($admin == Null) {
            return response()->json([
                'success' => false,
                'message' => 'data not found'
            ]);
        }

       // Deletion isn't allowed (feature disabled)
        return response()->json([
            'success' => false,
            'message' => 'Delete feature are not available in this module'
        ]);
    }


    /*---------------------------------------------------------
     | City Admin List (AJAX - DataTables)
     |----------------------------------------------------------
     | Returns paginated, searchable, and sortable City Admin
     |---------------------------------------------------------*/
    public function getAdminCityAdminListNew(Request $request){
        $start = $request->get("start"); // Get the starting record number for pagination
        $columnIndex = $request->get('order')[0]['column']; // Determine the index of the column to be sorted, based on the ordering array
        $searchValue = $request->get('search')['value']; // Extract the search value, which will be used to filter the data

        // Base query
        $records = Admin::query()->select('super_admin.*')->where('roles', '4');

        // Apply search filter
        if ($searchValue !== '') {
            $records->where(function ($query) use ($searchValue) {
                $query->where('super_admin.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('super_admin.email', 'like', '%' . $searchValue . '%');
            });
        }

        // Ordering
        if ($request->get('columns')[$columnIndex]['data'] != 'id'){
            $records->orderBy($request->get('columns')[$columnIndex]['data'], $request->get('order')[0]['dir']);
        }else{
            $records->orderBy('super_admin.id', 'desc');
        }

        // Get total records before pagination
        $totalRecords = $records->count();

        // Apply pagination
        $records = $records->skip($start)->take($request->get("length"))->get();

        // Format data for DataTable
        $data_arr = [];
        foreach ($records as $key => $record) {
            $id = $key + 1 + $start;
            $action_html = '<a  href="' . route('get:admin:edit_city_admin',[$record->id]) . '" style="margin: 0 7px;">
                            <img src="' . asset('/assets/images/template-images/writing-1.png') . '" style="width:20px; height: 20px;" title="Edit">
                        </a>';

            $data_arr[] = [
                "id" => $id,
                "name" => ucwords($record->name),
                "email" => User::Email2Stars($record->email),
                "action" => $action_html,
            ];
        }

        // Return DataTable response
        return response()->json([
            "draw" => $request->get('draw'),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr,
        ]);
    }

}
