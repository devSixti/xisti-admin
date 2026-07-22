<?php

namespace App\Http\Controllers;

use App\Classes\TokenClassApi;
use App\Classes\UserClassApi;
use App\Models\GeneralSettings;
use App\Models\PageSettings;
use App\Models\TransportDriverDetails;
use App\Models\TransportRideBook;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\WorldCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mpdf\Mpdf;

class HomeController extends Controller
{
    public function __construct(){
        //$this->middleware('auth');
    }

    //home Page Code
    public function index(){
        return view('home');
    }

    //Terms and conditions code
    public function getTermsAndConditions(Request $request)
    {
        //1=user;
        $get_page_data = PageSettings::query()->where('name', 'LIKE', "%terms%")->where('type', 1)->first();
        if ($get_page_data != Null) {
            return view('terms-and-conditions', compact('get_page_data'));
        }
        return view('terms-and-conditions');
    }

    //Privacy Policy code
    public function getPrivacyPolicy(Request $request)
    {
        //1=user;
        $title = "privacy policy";
        $get_page_data = PageSettings::query()->where('name', 'LIKE', "%privacy%")->where('type', 1)->first();
        if ($get_page_data != Null) {

            return view('terms-and-conditions', compact('get_page_data', 'title'));
        }
        return view('terms-and-conditions', compact('title'));
    }

    //Disclaimer code
    public function getDisclaimer(Request $request)
    {
        //1=user;
        $title = "disclaimer";
        $get_page_data = PageSettings::query()->where('name', 'LIKE', "%disclaimer%")->where('type', 1)->first();
        if ($get_page_data != Null) {

            return view('terms-and-conditions', compact('get_page_data', 'title'));
        }
        return view('terms-and-conditions', compact('title'));
    }

    //Faq code
    public function getFaq(Request $request)
    {
        //1=user;
        $title = "faq";
        $get_page_data = PageSettings::query()->where('name', 'LIKE', "%faq%")->where('type', 1)->first();
        if ($get_page_data != Null) {
            return view('terms-and-conditions', compact('get_page_data', 'title'));
        }
        return view('terms-and-conditions', compact('title'));
    }

    public function getWompiPaymentRedirect(Request $request)
    {
        $success_url = \Illuminate\Support\Facades\Route::has('payment.success') ? route('payment.success') : '/payments/success';
        $failed_url = \Illuminate\Support\Facades\Route::has('payment.failed') ? route('payment.failed') : '/payments/failed';

        $status = strtoupper(trim((string)$request->query('status', '')));
        if ($status === '') {
            $is_approved = strtolower(trim((string)$request->query('esAprobada', '')));
            if ($is_approved === 'true' || $is_approved === '1') {
                $status = 'APPROVED';
            } elseif ($is_approved === 'false' || $is_approved === '0') {
                $status = 'DECLINED';
            }
        }

        if ($status === '') {
            $transaction_id = trim((string)($request->query('id', $request->query('idTransaccion', ''))));
            if ($transaction_id !== '') {
                $general_settings = GeneralSettings::query()->first();
                if ($general_settings != null) {
                    $is_sandbox = (int)($general_settings->wompi_mode ?? 0) === 0;
                    $base_url = $is_sandbox
                        ? (($general_settings->wompi_sandbox_base_url ?? '') ?: 'https://sandbox.wompi.co/v1')
                        : (($general_settings->wompi_production_base_url ?? '') ?: 'https://production.wompi.co/v1');
                    $public_key = $is_sandbox
                        ? ($general_settings->wompi_sandbox_public_key ?? '')
                        : ($general_settings->wompi_production_public_key ?? '');
                    $public_key = trim((string)$public_key);

                    if ($public_key !== '') {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $public_key,
                            'Accept' => 'application/json',
                        ])->timeout(12)->get(rtrim($base_url, '/') . '/transactions/' . $transaction_id);

                        if ($response->successful()) {
                            $body = (array)$response->json();
                            $status = strtoupper(trim((string)($body['data']['status'] ?? '')));
                        }
                    }
                }
            }
        }

        if ($status === 'APPROVED') {
            return redirect($success_url);
        }

        return redirect($failed_url);
    }


    public function getFile($filename)
    {
        $icon = GeneralSettings::query()->first();
        if ($icon != Null) {
            $icon = asset('assets/images/website-logo-icon/' . $icon->website_favicon);
        } else {
            $icon = '';
        }
        return "<head><link rel='icon' href='' type='image/x-icon'></head><body style='margin: 0px; background: #0e0e0e; text-align: center;'><img style='-webkit-user-select: none; margin-left: auto;margin-right: auto; position: relative; top: 50%; transform: translateY(-50%);' src='" . asset('/assets/images/provider-documents/' . $filename) . "'></body>";
    }

    public function postDataDeletionStatus($reference){
        $user = User::query()->where('login_id',$reference)->first();
        if($user == Null){
            $provider = Provider::query()->where('login_id',$reference)->first();
            if($provider != NUll){
                return view('success_deletion');
            }
            return view('failed_deletion');
        } else{
            return view('success_deletion');
        }
    }

    /* -----------------------------------For Play-Store, App-Store Upload account_deletion-------------------------- */
    // provider_type = 1:store,2:driver,3:provider

    public function getAccountDeletion() {
        // account_deletion
        return view('account_deletion.login');
    }

    public function postAccountDeletion(Request $request) {
        $validator = Validator::make($request->all(),[
            'contact_number' => 'required',
            'full_number' => 'nullable',
            'country_code' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error',$validator->errors()->first());
        }
        $settings = \request()->get('general_settings');

        $contact_number = $request->get('contact_number');
        $country_code = $request->get('country_code');
        $user_type = "user";

        $user = User::query()->where('contact_number','=',$contact_number)
            ->where('country_code','=',$country_code)->whereNull('deleted_at')->first();
        if ($user == null) {
            return redirect()->route('get:account:deletion:login')->with('error','Your account is not registered with us please try again later.');
        }
        Auth::guard($user_type)->login($user);

        if ($settings->is_otp_verification != 0) {
            $response = (new TokenClassApi())->sendUserSmsVerification(Auth::guard($user_type)->id());
        }


        return redirect()->route('get:account:deletion:verification')->with('success','Otp Send Successfully!');
    }

    public function getAccountDeletionVerification(Request $request) {
        $guard = "";
        if (Auth::guard('user')->check()){
            $guard = 'user';
        } else {
            Auth::logout();
            return redirect()->route('get:account:deletion:login')->with('error','Something went wrong');
        }
        return view('account_deletion.verification',compact('guard'));
    }

    public function postAccountDeletionVerification(Request $request) {
        $guard = "";
        if (Auth::guard('user')->check()){
            $guard = 'user';
            $user_details = User::query()->where('id',Auth::guard($guard)->id())->whereNull('deleted_at')->first();
            $get_otp = UserVerification::query()->where('user_id', "=", $user_details->id)->first();
        }else {
            Auth::logout();
            return redirect()->route('get:account:deletion:login')->with('error','Something went wrong');
        }

        $otp = $request->get('otp_1').$request->get('otp_2').$request->get('otp_3').$request->get('otp_4');
        if ($otp == "1234") {
            $user_details->verified_at = date('Y-m-d H:i:s');
            $user_details->save();

            return redirect()->route('get:account:deletion:profile');
        } else {
            return redirect()->back()->with('error','Invalid OTP');
        }
    }

    public function getAccountDeletionRensendVerificationCode(Request $request) {
//        $settings = \request()->get('general_settings');
//        if ($settings->is_otp_verification != 0) {
            $guard = ""; $id = 0;
            if (Auth::guard('user')->check()) {
                $guard = 'user';
                $id = Auth::guard($guard)->id();

                if ($id > 0) {
                    (new TokenClassApi())->sendUserSmsVerification($id);
                }

                return redirect()->back()->with('success', 'Otp sent successfully!');
            }else {
                Auth::logout();
                return redirect()->route('get:account:deletion:login')->with('error', 'Something went wrong');
            }
//        }

    }

    public function getAccountDeletionLogout($guard) {
        Auth::guard($guard)->logout();
        return redirect()->route('get:account:deletion:login');
    }

    public function getAccountDeletionProfile() {
        $guard = "";
        if (Auth::guard('user')->check()){
            $guard = 'user';
            $user_details = User::query()->where('id',Auth::guard($guard)->id())->whereNull('deleted_at')->first();
        }else {
            Auth::logout();
            return redirect()->route('get:account:deletion:login')->with('error','Something went wrong');
        }

        return view('account_deletion.profile',compact('guard','user_details'));
    }

    public function postAccountDeletionDeleteAccount(Request $request) {
        $guard=$request->get('guard');
        $id=$request->get('id');

        if ($guard == "user") {
            app(UserClassApi::class)->forfeitWalletBalanceOnAccountDeletion((int) $id);
            User::query()->whereKey($id)->delete();
        }
        Auth::logout();
        return response()->json(['success' => true, 'message' => 'Account deleted successfully']);
//        return redirect()->route('get:account:deletion:login')->with('success','Account Delete Successfully!');
    }

    //social Login
    public function redirectToProvider($guards,$provider)
    {
        return Socialite::driver($provider)->with(['state' => $guards])->redirect();
    }
    //social Login callback function
    public function handleCallback(Request $request, $provider) {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (InvalidStateException $e) {
            $user = Socialite::driver($provider)->stateless()->user();
        }
        $guards = $request->get("state");

       if($guards == "user") {
            $userstore = User::query()->where('login_id',$user->id)->first();
            if ($userstore == null) {
                return redirect()->route('get:account:deletion:login')->with('error','Your account is not registered with us please try again later.');
            }
        }
        else {
            return redirect()->route("get:account:deletion:login");
        }

        Auth::guard($guards)->login($userstore);

        return redirect()->route('get:account:deletion:profile');
    }


    /* -----------------------------------End For Play-Store, App-Store Upload account_deletion---------------------- */

    public static function currencyConvert($to_currency="",$amount=0,$with_sysmbol = 0,$from_currency=""){

        //from currency is for feature ref
        if($from_currency == "")
        {
            $default_currency = \App\Support\UserCurrencyResolver::forCurrency($to_currency);
        }
        if($default_currency != Null)
        {
            $amount = $amount * $default_currency->ratio;
        }

        $amount = round($amount,2);

        if($with_sysmbol == 0)
        {
            return $amount;
        }

        return $to_currency." ".$amount;
    }
    // invoice for ride invoice download
    public function getRideInvoiceDownload(Request $request,$order_id=0,$provider_type="",$provider_id=0) {
        // $order_id order id, Provider_type = "user","store","driver","provider"
        $user_currency = "$";
        $user_language = "en";

        $ride_details =  TransportRideBook::query()->select('user_ride_booking.*');
        if($provider_type == "user"){
            $view_file =  'invoice.user-ride-invoice';
            $ride_details= $ride_details->where('user_ride_booking.user_id',$provider_id);
//            $user_details = User::query()->where('id',$provider_id)->first();
//            if ($user_details != Null){
//                $provider_timezone = $user_details->time_zone;
//            }

        }
        elseif($provider_type == "driver"){
            //driver detail id
            $view_file =  'invoice.driver-ride-invoice';
            $ride_details= $ride_details->where('user_ride_booking.driver_id',$provider_id);
            $provider_data = TransportDriverDetails::query()->select('users.id','users.currency','users.language')
                ->join('users','transport_driver_details.user_id', '=', 'users.id')
                ->where('transport_driver_details.user_id','=',$provider_id)->first();
            if($provider_data != NUll){
                $user_currency = isset($provider_data->currency) ? $provider_data->currency : "$";
                $user_language = isset($provider_data->language) ? $provider_data->language : "en";
//                $provider_timezone = isset($provider_data->time_zone) ? $provider_data->time_zone : "";
            }
        }
        else{
            $view_file =  'invoice.user-ride-invoice';
            $ride_details= $ride_details->where('user_ride_booking.user_id',$provider_id);
        }
        $ride_details=$ride_details
            ->where('user_ride_booking.id','=',$order_id)
            ->whereIn('user_ride_booking.status',[4,9,10])
            ->first();

        if($ride_details != Null)
        {
            //Fetching general settings
//            $general_settings = request()->get("general_settings");
//            $default_server_timezone = "";
//            if ($general_settings != Null) {
//                if ($general_settings->default_server_timezone != "") {
//                    $default_server_timezone= $general_settings->default_server_timezone;
//                }
//            }
//            $timezone = $provider_timezone != Null ? $provider_timezone : $default_server_timezone;
            $get_user_details =  User::query()->select('first_name','email','contact_number','country_code','currency','language')->where('id',$ride_details->user_id)->first();
            $user_name = $email = $contact_number ="";
            if($get_user_details != NUll){
                $user_name = isset($get_user_details->first_name)?$get_user_details->first_name:"--";
                $email = isset($get_user_details->email)?$get_user_details->email:"--";
                $contact_number = isset($get_user_details->contact_number)?$get_user_details->country_code."".$get_user_details->contact_number:"--";
                if($provider_type == "user") {
                    $user_currency = isset($get_user_details->currency) ? $get_user_details->currency : "$";
                    $user_language = isset($get_user_details->language) ? $get_user_details->language : "en";
                }
            }
            $ride_no =  isset($ride_details->ride_no)?$ride_details->ride_no:"--";
            $driver_details = TransportDriverDetails::query()->select('transport_driver_details.id','users.first_name as driver_name', 'users.email', 'users.contact_number','users.country_code')
                ->join('users', 'users.id', 'transport_driver_details.user_id')
                ->where('transport_driver_details.user_id', $ride_details->driver_id)
                ->first();
            $driver_name = "--";
            if($driver_details != Null){
                $driver_name = $driver_details->driver_name;
            }

            $order_status =  (isset($ride_details->status) && ($ride_details->status == 9))?__('user_messages.365',[],$user_language):
                ((isset($ride_details->status) && ($ride_details->status == 4))?__('user_messages.366',[],$user_language):
                    ((isset($ride_details->status) && ($ride_details->status == 3))?__('user_messages.366',[],$user_language):""));

            $payment_type =  (isset($ride_details->payment_type ) && ($ride_details->payment_type == 1))?__('user_messages.367',[],$user_language):
                (((isset($ride_details->payment_type ) && ($ride_details->payment_type == 2)))?__('user_messages.368',[],$user_language):
                    (((isset($ride_details->payment_type ) && ($ride_details->payment_type == 3)))?__('user_messages.369',[],$user_language):""));

            $pickup_address =  isset($ride_details->pickup_address)?trim($ride_details->pickup_address):"--";
            $destination_address =  isset($ride_details->destination_address)?trim($ride_details->destination_address):"--";

//            $notificationClass = new NotificationClass();
//            $order_date = $notificationClass->convertTimezone($ride_details->created_at,"",$timezone, "s");
//            $pickup_date = $notificationClass->convertTimezone($ride_details->pickup_datetime,"",$timezone, "s");
//            $destination_date = $notificationClass->convertTimezone($ride_details->destination_datetime,"",$timezone, "s");

            $order_date = $ride_details->created_at;

            $tripAmountBase = \App\Helpers\TripAmountHelper::resolveBase($ride_details);
            $total_pay = $this->currencyConvert($user_currency, $tripAmountBase, 1, "");

            $total_toll_charge = (isset($ride_details->toll_charge) && $ride_details->toll_charge > 0)?$ride_details->toll_charge:0;
            $total_toll_charge = $this->currencyConvert($user_currency, $total_toll_charge, 1, "");

            $trip_value = $tripAmountBase;
            $settings = request()->get("general_settings");
            $invoice = \App\Helpers\RideInvoiceHelper::breakdownForRide($ride_details, $settings);
            $commission_amount = $invoice['commission_amount'];
            $vat_on_commission = $invoice['vat_on_commission'];
            $total_deduction = $invoice['total_deduction'];
            $net_driver_pay = $invoice['net_driver_pay'];
            $web_site_logo = (file_exists(public_path('assets/images/website-logo-icon/'.$settings->website_logo)) == true) ? asset('assets/images/website-logo-icon/'.$settings->website_logo) : "";

            $data = [
                'web_site_logo' => $web_site_logo,
                'user_name' => ($ride_details->is_hail == 1) ? $ride_details->other_user_name : $user_name,
                'email' => $email,
                'contact_number' => $contact_number,
                'driver_name' => $driver_name,
                'ride_no' => $ride_no,
                'order_status' => $order_status,
                'payment_type' => $payment_type,
                'to_currency' => $user_currency,
                'user_language' => $user_language,
                'pickup_address' => $pickup_address,
                'destination_address' => $destination_address,
                'order_date' => $order_date,
                'total_pay' => $total_pay,
                'total_toll_charge' => $total_toll_charge,
                'trip_value' => $this->currencyConvert($user_currency, $trip_value, 1, ""),
                'commission_amount' => $this->currencyConvert($user_currency, $commission_amount, 1, ""),
                'vat_on_commission' => $this->currencyConvert($user_currency, $vat_on_commission, 1, ""),
                'total_deduction' => $this->currencyConvert($user_currency, $total_deduction, 1, ""),
                'net_driver_pay' => $this->currencyConvert($user_currency, $net_driver_pay, 1, ""),
            ];

            try {
                //mpdf
                $mpdf = New Mpdf([
                    'mode' => 'utf-8',
                    'tempDir' => storage_path('mpdf-temp'),
                ]);
                $mpdf->autoScriptToLang=true;
                $mpdf->autoLangToFont=true;
                $mpdf->autoArabic = true;
                $html_data =  view($view_file,$data);
                $mpdf->WriteHTML($html_data);
                $mpdf->Output($ride_no.".pdf", 'I');

            }catch (\Exception $e){
                info($e->getMessage());
            }
        }else{
            return false;
        }
    }
}
