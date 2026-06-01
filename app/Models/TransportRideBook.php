<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRideBook extends Model
{
    protected $table = 'user_ride_booking';

    public function generateRideNo()
    {
        $ride_no = random_int(1, 9) . date('sihYdm') . random_int(1, 9);
        return $ride_no;
    }

    public function generateOtp($digits = 4)
    {
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return $otp;
    }



    public function BookingCostCount($base_fare, $total_distance_amount, $promo_code_discount, $get_tax, $admin_commission, $min_fare_amount, $time_fare_amount, $surcharge_cost = 0,$get_refer_discount_price=0)
    {
        //client req

        $step_1 = $base_fare + $total_distance_amount + $time_fare_amount + $surcharge_cost; //ride amount
        $step_2 = round(($step_1), 2); //add adjust amount
        $step_3 = $step_2 - $promo_code_discount - $get_refer_discount_price; //promo code discount
        $step_4 = round((($step_3 * $get_tax) / 100), 2); //tax count
        $step_5 = $step_3 + $step_4 + $this->tip; //count ride amount


        $this->sub_total = $step_3;

        $this->tax = $step_4;
        $this->save();

        $round_module = 0;
        $rounding_amount_module = GeneralSettings::query()->select('rounding_amount_module')->first();
        if ($rounding_amount_module != Null) {
            $round_module = $rounding_amount_module->rounding_amount_module;
        }
        $this->save();
        if ($round_module == 1) {
            $this->total_pay = round($step_5);
            $this->save();
        } else {
            $this->total_pay = round($step_5,2);
            $this->save();
        }
        $this->save();

        //commission calculation
        $driver_amount = $step_1 ;
        $commission = round((($driver_amount * $admin_commission) / 100), 2);
        $driver_new_amount = $driver_amount - $commission;
        $admin_commission = ($commission - $promo_code_discount) - $get_refer_discount_price;

        $this->driver_amount = round($driver_new_amount + $this->tip, 2);
        $this->save();

        $this->admin_commission = round($admin_commission, 2);
        $this->save();

    }
}
