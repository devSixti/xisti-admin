<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBid extends Model
{
    use HasFactory;
    protected $table = "driver_ride_bid_amount";
    protected $fillable =['id','driver_id','user_id','ride_id','vehicle_type_id','offered_price','status','bidding_time'];
}
