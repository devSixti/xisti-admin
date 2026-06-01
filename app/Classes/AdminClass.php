<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 01-01-2019
 * Time: 04:32 PM
 */

namespace App\Classes;


use App\Models\LanguageLists;
use App\Models\ProviderUserRunningService;
use App\Models\ServiceSettings;
use App\Models\TransportDriverDetails;
use App\Models\TransportRideBook;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminClass
{
    private $all;
    private $available;
    private $ride_start;
    private $reached;
    private $enroute;

    public function __construct()
    {
        $this->all = [0, 4, 9, 10];
        $this->available = [0, 4, 9, 10];
        $this->ride_start = [5, 6, 7, 8];
        $this->reached = [3];
        $this->enroute = [1, 2,];
    }

    public function renderingResponce($view)
    {
        return response()->json([
            'content' => $view['page-content'],
            'title' => $view['title'],
            'extra_css' => $view['page-css'],
            'extra_js' => $view['page-js'],
        ]);
    }


    // Transport SCW Service Category Wise Provider Find
    public function ServiceSettingsStore($request)
    {
        $id = $request->get('id');
        if ($id != Null) {
            $service_settings = ServiceSettings::where('id', $request->get('id'))->first();
        } else {
            $service_settings = new ServiceSettings();
        }
        $service_settings->provider_search_radius = $request->get('provider_search_radius');
        $service_settings->admin_commission = $request->get('admin_commission');
        $service_settings->admin_hail_commission = $request->get('admin_hail_commission');
        $service_settings->driver_timeout = $request->get('driver_timeout');
        $service_settings->ride_expiry = $request->get('ride_expiry');
        $service_settings->nearest_ride_popup = $request->get('nearest_ride_popup');
        $service_settings->save();
        return $service_settings;
    }



    public function AvailableProviderLocation($check_type = Null, $search_provider_name = Null)
    {
        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        $transport_provider_check = [];
        $driver_array = [];
        $check_available = array_merge($this->ride_start, $this->reached, $this->enroute);
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::where('id', $running->booking_id)
                ->whereIn('status', $check_available)
                ->first();
            if ($transport_running_service[$running->provider_id] != Null) {
                $transport_provider_check[] = $running->provider_id;
            }

        }
        $driver_array = array_merge($driver_array, $transport_provider_check);

        $providers_detials_check = TransportDriverDetails::query()->select(
            'transport_driver_details.id',
            'users.id as provider_id', DB::raw("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) as name"), 'users.last_name as last_name', 'users.contact_number',
            'users.country_code',
            'users.avatar','users.driver_current_status',
            'transport_driver_details.current_lat',
            'transport_driver_details.current_long',
            'transport_driver_details.plat_no',
            'transport_driver_details.availability_ride_status')
            ->join('users', 'users.id', '=','transport_driver_details.user_id')
            ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
            ->whereNull('users.deleted_at');
        if ($search_provider_name != Null) {
            $providers_detials_check->where(function($query) use ($search_provider_name){
                $query->orWhere('users.first_name', 'LIKE', "%$search_provider_name%")
                    ->orWhere('users.last_name', 'LIKE', "%$search_provider_name%");
            });
        }
        $providers_detials = $providers_detials_check
            ->where('users.status', 1)
            ->where('users.driver_current_status',1)
            ->whereNotIn('users.id', $driver_array)
            ->whereNotNull('users.access_token')
            ->get();
        $availability_ride_status = 3;
        $providers = [];
        if ($providers_detials->isEmpty()) {
            $providers = [];
        } else {

            foreach ($providers_detials as $key => $provider) {
                $providers[] = [
                    "id" => $provider->id,
                    "name" => $provider->name,
                    "last_name" => Null,
                    "image" => $provider->avatar,
                    "latitude" => $provider->current_lat,
                    "longitude" => $provider->current_long,
                    "address" => "",
                    "ride_status" => $availability_ride_status,
                    "phone" =>  $provider->country_code.$provider->contact_number,
                    "plat_no" =>  $provider->plat_no,
                ];
            }
        }
        return $providers;
    }

    public function AllProviderLocation($check_type = Null, $search_provider_name = Null)
    {
        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::select('status')->where('id', $running->booking_id)->first();
        }
        $providers_detials_check = TransportDriverDetails::query()->select(
            'transport_driver_details.id',
            'users.id as provider_id', DB::raw("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) as name"), 'users.last_name', 'users.contact_number','users.country_code',
            'users.avatar','users.driver_current_status',
            'transport_driver_details.current_lat',
            'transport_driver_details.current_long',
            'transport_driver_details.plat_no',
            'transport_driver_details.availability_ride_status',
        )
            ->join('users', 'users.id','=', 'transport_driver_details.user_id')
            ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
            ->whereNull('users.deleted_at');
        if ($search_provider_name != Null) {
            $providers_detials_check->where(function($query) use ($search_provider_name){
                $query->orWhere('users.first_name', 'LIKE', "%$search_provider_name%")
                    ->orWhere('users.last_name', 'LIKE', "%$search_provider_name%");
            });
        }
        $providers_detials = $providers_detials_check
            ->where('users.status', 1)
            ->where('users.driver_current_status', 1)
            ->whereNotNull('users.access_token')
            ->get();
        $availability_ride_status = 3;
        $providers = [];
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
                    "phone" => $provider->country_code.$provider->contact_number,
                    "plat_no" =>$provider->plat_no,
                ];
            }
        }
        return $providers;
    }

    public function RideStartProviderLocation($check_type = Null, $search_provider_name = Null)
    {
        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        $driver_array = [];
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::query()->where('id', $running->booking_id)
                ->whereIn('status', $this->ride_start)
                ->first();
        }
        $driver_array = array_merge($driver_array, $transport_running_service);
        if (!count($driver_array) > 0) {
            return $providers = [];
        }
        $use_tran = [];
        $use_delivery = [];
        foreach ($driver_array as $key => $driver) {
            if ($driver != Null) {
                $providers_delivery_check = TransportDriverDetails::query()->select(
                    'transport_driver_details.id',
                    'u1.id as provider_id', DB::raw("CONCAT(COALESCE(u1.first_name,''),' ',COALESCE(u1.last_name,'')) as name"), 'u1.last_name', 'u1.contact_number', 'u1.country_code',
                    'u1.avatar',
                    'transport_driver_details.current_lat',
                    'transport_driver_details.current_long',
                    'transport_driver_details.plat_no',
                    'transport_driver_details.availability_ride_status',
                    'u2.first_name as customer_first_name', 'u2.last_name as customer_last_name',
                    'u2.country_code as customer_country_code',
                    'u2.contact_number as customer_contact_number', 'u2.id as customer_id',
                    'user_ride_booking.destination_address', 'user_ride_booking.pickup_address'
                )
                    ->join('users as u1', 'u1.id', '=','transport_driver_details.user_id')
                    ->join('user_ride_booking', 'user_ride_booking.driver_id', '=', 'transport_driver_details.user_id')
                    ->leftjoin('users as u2', 'u2.id', '=', 'user_ride_booking.user_id')
                    ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
                    ->whereNull('u1.deleted_at')
                    ->whereNull('u2.deleted_at');
                if ($search_provider_name != Null) {
                    $providers_delivery_check->where(function($query) use ($search_provider_name){
                        $query->orWhere('u1.first_name', 'LIKE', "%$search_provider_name%")
                            ->orWhere('u1.last_name', 'LIKE', "%$search_provider_name%");
                    });
                }
                $providers_delivery = $providers_delivery_check->where('transport_driver_details.user_id', $driver->driver_id)
                    ->where('u1.status', 1)
                    ->whereNotNull('u1.access_token')
                    ->orderBy('user_ride_booking.id','desc')
                    ->first();
                if ($providers_delivery != Null) {
                    $use_delivery[] = array(
                        "id" => $providers_delivery->id,
                        "name" => $providers_delivery->name,
                        "last_name" => Null,
                        "image" => $providers_delivery->avatar,
                        "latitude" => $providers_delivery->current_lat,
                        "longitude" => $providers_delivery->current_long,
                        "address" => "",
                        "ride_status" => 2,
                        "phone" => $providers_delivery->country_code.User::ContactNumber2Stars($providers_delivery->contact_number),
                        "service_cat_id" => $providers_delivery->service_cat_id,

                        "pickup_address" => $providers_delivery->pickup_address,
                        "destination_address" => $providers_delivery->destination_address,
                        "user_id" => $providers_delivery->customer_id,
                        "user_name" => $providers_delivery->customer_first_name,
                        "user_last_name" => $providers_delivery->customer_last_name,
                        "rider_phone" => $providers_delivery->customer_country_code.User::ContactNumber2Stars($providers_delivery->customer_contact_number),
                        "plat_no" =>$providers_delivery->plat_no,
                    );
                }
            }
        }
        $providers = array_merge($use_tran, $use_delivery);
        return $providers;
    }

    public function RideReachedProviderLocation($check_type = Null, $search_provider_name = Null)
    {
        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        $driver_array = [];
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::where('id', $running->booking_id)
                ->whereIn('status', $this->reached)
                ->first();
        }
        $driver_array = array_merge($driver_array, $transport_running_service);
        if (!count($driver_array) > 0) {
            return $providers = [];
        }
        $use_tran = [];
        $use_delivery = [];
        foreach ($driver_array as $key => $driver) {
            if ($driver != Null) {
                $providers_delivery_check = TransportDriverDetails::query()->select(
                    'transport_driver_details.id',
                    'u1.id as provider_id', DB::raw("CONCAT(COALESCE(u1.first_name,''),' ',COALESCE(u1.last_name,'')) as name"), 'u1.last_name', 'u1.contact_number', 'u1.country_code',
                    'u1.avatar',
                    'transport_driver_details.current_lat',
                    'transport_driver_details.current_long',
                    'transport_driver_details.plat_no',
                    'transport_driver_details.availability_ride_status',
                    'u2.first_name as customer_first_name', 'u2.last_name as customer_last_name',
                    'u2.country_code as customer_country_code',
                    'u2.contact_number as customer_contact_number', 'u2.id as customer_id',
                    'user_ride_booking.destination_address', 'user_ride_booking.pickup_address'
                )
                    ->join('users as u1', 'u1.id', '=','transport_driver_details.user_id')
                    ->join('user_ride_booking', 'user_ride_booking.driver_id', '=', 'transport_driver_details.user_id')
                    ->join('users as u2', 'u2.id', '=', 'user_ride_booking.user_id')
                    ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
                    ->whereNull('u1.deleted_at')
                    ->whereNull('u2.deleted_at');
                if ($search_provider_name != Null) {
                    $providers_delivery_check->where(function($query) use ($search_provider_name){
                        $query->orWhere('u1.first_name', 'LIKE', "%$search_provider_name%")
                            ->orWhere('u1.last_name', 'LIKE', "%$search_provider_name%");
                    });
                }
                $providers_delivery = $providers_delivery_check->where('transport_driver_details.user_id', $driver->driver_id)
                    ->where('u1.status', 1)
                    ->whereNotNull('u1.access_token')
                    ->orderBy('user_ride_booking.id','desc')
                    ->first();
                if ($providers_delivery != Null) {
                    $use_delivery[] = array(
                        "id" => $providers_delivery->id,
                        "name" => $providers_delivery->name,
                        "last_name" => Null,
                        "image" => $providers_delivery->avatar,
                        "latitude" => $providers_delivery->current_lat,
                        "longitude" => $providers_delivery->current_long,
                        "address" => "",
                        "ride_status" => 1,
                        "phone" => $providers_delivery->country_code.User::ContactNumber2Stars($providers_delivery->contact_number),
                        "service_cat_id" => $providers_delivery->service_cat_id,

                        "pickup_address" => $providers_delivery->pickup_address,
                        "destination_address" => $providers_delivery->destination_address,
                        "user_id" => $providers_delivery->customer_id,
                        "user_name" => $providers_delivery->customer_first_name,
                        "user_last_name" => $providers_delivery->customer_last_name,
                        "rider_phone" => $providers_delivery->customer_country_code.User::ContactNumber2Stars($providers_delivery->customer_contact_number),
                        "plat_no" =>$providers_delivery->plat_no
                    );
                }

            }
        }
        $providers = array_merge($use_tran, $use_delivery);
        return $providers;
    }

    public function RideEnrouteProviderLocation($check_type = Null, $search_provider_name = Null)
    {
        $expire_date_time = date('Y-m-d h:i:s', strtotime("-120 minutes"));
        $running_service = ProviderUserRunningService::all();
        $transport_running_service = [];
        $driver_array = [];
        foreach ($running_service as $key => $running) {
            $transport_running_service[$running->provider_id] = TransportRideBook::where('id', $running->booking_id)
                ->whereIn('status', $this->enroute)
                ->first();
        }
        $driver_array = array_merge($driver_array, $transport_running_service);
        if (!count($driver_array) > 0) {
            return $providers = [];
        }
        $use_tran = [];
        $use_delivery = [];
        foreach ($driver_array as $key => $driver) {
            if ($driver != Null) {
                $providers_delivery_check = TransportDriverDetails::query()->select(
                    'transport_driver_details.id',
                    'u1.id as provider_id', DB::raw("CONCAT(COALESCE(u1.first_name,''),' ',COALESCE(u1.last_name,'')) as name"), 'u1.last_name', 'u1.contact_number', 'u1.country_code',
                    'u1.avatar',
                    'transport_driver_details.current_lat',
                    'transport_driver_details.current_long',
                    'transport_driver_details.plat_no',
                    'transport_driver_details.availability_ride_status',
                    'u2.first_name as customer_first_name', 'u2.last_name as customer_last_name',
                    'u2.country_code as customer_country_code',
                    'u2.contact_number as customer_contact_number', 'u2.id as customer_id',
                    'user_ride_booking.destination_address', 'user_ride_booking.pickup_address'
                )
                    ->join('users as u1', 'u1.id','=', 'transport_driver_details.user_id')
                    ->join('user_ride_booking', 'user_ride_booking.driver_id', '=', 'transport_driver_details.user_id')
                    ->join('users as u2', 'u2.id', '=', 'user_ride_booking.user_id')
                    ->where('transport_driver_details.last_online_date_time', '>=', $expire_date_time)
                    ->whereNull('u1.deleted_at')
                    ->whereNull('u2.deleted_at');
                if ($search_provider_name != Null) {
                    $providers_delivery_check->where(function($query) use ($search_provider_name){
                        $query->orWhere('u1.first_name', 'LIKE', "%$search_provider_name%")
                            ->orWhere('u1.last_name', 'LIKE', "%$search_provider_name%");
                    });
                }
                $providers_delivery = $providers_delivery_check->where('transport_driver_details.user_id', $driver->driver_id)
                    ->where('u1.status', 1)
                    ->whereNotNull('u1.access_token')
                    ->orderBy('user_ride_booking.id','desc')
                    ->first();
                if ($providers_delivery != Null) {
                    $use_delivery[] = array(
                        "id" => $providers_delivery->id,
                        "name" => $providers_delivery->name,
                        "last_name" => Null,
                        "image" => $providers_delivery->avatar,
                        "latitude" => $providers_delivery->current_lat,
                        "longitude" => $providers_delivery->current_long,
                        "address" => "",
                        "ride_status" => 0,
                        "phone" => $providers_delivery->country_code.$providers_delivery->contact_number,
                        "service_cat_id" => $providers_delivery->service_cat_id,
                        "pickup_address" => $providers_delivery->pickup_address,
                        "destination_address" => $providers_delivery->destination_address,
                        "user_id" => $providers_delivery->customer_id,
                        "user_name" => $providers_delivery->customer_first_name,
                        "user_last_name" => $providers_delivery->customer_last_name,
                        "rider_phone" => $providers_delivery->customer_country_code.$providers_delivery->customer_contact_number,
                        "plat_no" =>$providers_delivery->plat_no
                    );
                }
            }
        }
        $providers = array_merge($use_tran, $use_delivery);
        return $providers;
    }
    public function get_langugae_fields($user_lang="en")
    {

        $get_lang = LanguageLists::query()->where('language_code','=',$user_lang)->first();
        if($get_lang != Null){
            if(isset($get_lang->language_code) &&  $get_lang->language_code != Null){
                $lang_prefix = $get_lang->language_code."_";
            }else {
                $lang_prefix = "";
            }
        }else {
            $lang_prefix = "";
        }
        return $lang_prefix;
    }

    public function is_in_restricted_area($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y){
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon-1; $i < $points_polygon; $j = $i++) {
            if ((($vertices_y[$i] > $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
                ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])))
                $c = !$c;
        }
        return $c;
    }
}
