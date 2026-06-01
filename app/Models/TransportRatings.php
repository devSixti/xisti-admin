<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRatings extends Model
{
    protected $table = 'transport_driver_rating';

    public static function getTransportServiceCatName($ride_id)
    {
        $service_cat_name = "-----";
        $get_service = TransportRideBook::query()->select('service_category.name as service_cat_name')
            ->join('service_category', 'service_category.id', '=', 'user_ride_booking.service_cat_id')
            ->where('user_ride_booking.id', '=', $ride_id)->first();
        if($get_service != Null)
        {
            $service_cat_name = $get_service->service_cat_name;
        }
        return $service_cat_name;
    }

    public static function getDeliveryServiceCatName($delivery_id)
    {
        $service_cat_name = "-----";
        $get_service = UserProductBooking::query()->select('service_category.name as service_cat_name')
            ->join('service_category', 'service_category.id', '=', 'user_store_product_booking.service_cat_id')
            ->where('user_store_product_booking.id', '=', $delivery_id)->first();
        if($get_service != Null)
        {
            $service_cat_name = $get_service->service_cat_name;
        }
        return $service_cat_name;
    }

    public static function getTransportRentalServiceCatName($rental_id)
    {
        $service_cat_name = "-----";
        $get_service = TransportRentalRideBooking::query()->select('service_category.name as service_cat_name')
            ->join('service_category', 'service_category.id', '=', 'user_rental_ride_booking.service_cat_id')
            ->where('user_rental_ride_booking.id', '=', $rental_id)->first();
        if($get_service != Null)
        {
            $service_cat_name = $get_service->service_cat_name;
        }
        return $service_cat_name;
    }
}
