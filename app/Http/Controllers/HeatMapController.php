<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\TransportDriverDetails;
use App\Models\TransportRideBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HeatMapController extends Controller
{
    //
    public function getAdminTransportHeatMap(Request $request)
    {
        $redirect_type = $request->get("redirect_type");
        $latlong_array = TransportRideBook::query()->select(DB::raw("CONCAT(pickup_lat, ',', pickup_long) as pickup_latlong"))->whereNotNull('pickup_lat')->whereNotNull('pickup_long')->pluck('pickup_latlong')->toArray();

        return view('admin.pages.super_admin.heat_map.transport_heat_map', compact( 'latlong_array'));
    }

    public function postDriverHeatMap(Request $request)
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

        $driver_id = $request->get('user_id');
        $map_url =  route('heat.map',[$driver_id]);

        return response()->json([
            "status" => 1,
            'message' => __('driver_messages.1'),
            "message_code" => 1,
            "heat_map_url" => $map_url
        ]);
    }

    public function postDriverWebViewHeatMap($driver_id)
    {
        $driver = TransportDriverDetails::with(["getUserDetails:id,language"])->where('user_id',$driver_id)->first();
        $driver_lat = $driver->current_lat;
        $driver_long = $driver->current_long;
        $filter_key = 3;
        $latlong_array = $this->getLatLong($filter_key);

        $userLanguage = $driver->getUserDetails->language ?? "en";

        return view('admin.pages.super_admin.heat_map.app_heat_map', compact('latlong_array','driver_lat','driver_long','filter_key', 'userLanguage'));
    }

    public function getLatLong($filter_key=3){
        $date = date('Y-m-d');
        if ($filter_key == 1) {
            //today
            $start_date = $date;
            $end_date = $date;
        } elseif ($filter_key == 2) {
            //last 7 day
            $start_date = date('Y-m-d', strtotime('-7 days', strtotime($date)));
            $end_date = $date;
        } elseif ($filter_key == 3) {
            //last 30 day

            $start_date = date('Y-m-d', strtotime('-30 days', strtotime($date)));
            $end_date = $date;
        } else {
            return redirect()->route('cancel.heat.map');
        }
        $heat_map_lat_long_array = [];
        $latlong_array = TransportRideBook::query()
            ->select(
                'id',
                DB::raw("CONCAT(pickup_lat, ',', pickup_long) as pickup_latlong"),
                DB::raw("CONCAT(TRUNCATE(SUBSTRING_INDEX(pickup_lat,',',1),2), ',', TRUNCATE(SUBSTRING_INDEX(pickup_long,',',-1),2)) AS latitude_longitude")
            )
            ->whereNotNull('pickup_lat')
            ->whereNotNull('pickup_long')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get();
        $latlong_array = $this->calculateWeightageForHeatMap($latlong_array);
        return $latlong_array;
    }

    public function calculateWeightageForHeatMap($latlong_array){
        /* Percentage need to increase for minority and majority orders */
        $minority_order_percentage = 2;
        $majority_order_weightage = 1;
        $total_unique_locations=$latlong_array->unique('latitude_longitude')->values();

        $total_rides=$latlong_array->pluck('pickup_latlong')->count();

        $percentage = ($total_rides * $minority_order_percentage)/100;

        $ride_lat_long=collect();
        /* Iterate through all unique orders */
        $total_unique_locations->each(function ($v)use(&$latlong_array,$total_rides,$total_unique_locations,$percentage,&$ride_lat_long,$majority_order_weightage){
            /* To Find Major and Minor Order/Rides:-
             * find orders from all orders through unique list
             * compare it with unique order list if orders lesser than total unique order than it will minor
             * if bigger than it will major
             */
            $total_order_from_unique_list = $latlong_array->where('latitude_longitude','=',$v->latitude_longitude);
            if ($total_order_from_unique_list->count() <= $total_unique_locations->count()){
                /* Minority Orders */
                $weightage = $percentage;
            }else{
                /* Majority Orders */
                $weightage = $majority_order_weightage;
            }

            $ride_lat_long = $ride_lat_long->merge(
                $total_order_from_unique_list->crossJoin(['weight'=>$weightage])
            );
        });
        return $ride_lat_long;
    }

    public function postAjaxDriverWebViewHeatMap(Request $request){
        $filter_key =  $request->get('filter_key');
        $latlong_array = $this->getLatLong($filter_key);

        return response()->json([
            'success' => true,
            'latlong_array' => $latlong_array,
        ]);
    }
    public function postDriverWebViewCancelHeatMap(){
        return false;
    }
}
