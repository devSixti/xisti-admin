<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportVehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $transport_vehicle_type_record =[
            [
                'id' => 1,
                'service_id' => 1,
                'name' => 'Hatchback',
                'icon_name' => '209151420200710.png',
                'cost_for_km' => 40.00,
                'weight_limit' => Null,
                'width_limit' => Null,
                'height_limit' => Null,
                'dimension_limit' => Null,
                'base_fare' => 300.00,
                'time_fare' => 15.00,
                'min_fare_amount' => 500.00,
                'status' => 1
            ],
            [
                'id' => 2,
                'service_id' => 1,
                'name' => 'SUV',
                'icon_name' => '231141420200710.png',
                'cost_for_km' => 25.00,
                'weight_limit' => Null,
                'width_limit' => Null,
                'height_limit' => Null,
                'dimension_limit' => Null,
                'base_fare' => 150.00,
                'time_fare' => 10.00,
                'min_fare_amount' => 300.00,
                'status' => 1
            ],
            [
                'id' => 3,
                'service_id' => 1,
                'name' => 'Luxury',
                'icon_name' => '251141420200710.png',
                'cost_for_km' => 8.00,
                'weight_limit' => Null,
                'width_limit' => Null,
                'height_limit' => Null,
                'dimension_limit' => Null,
                'base_fare' => 50.00,
                'time_fare' => 10.00,
                'min_fare_amount' => 100.00,
                'status' => 1
            ],
            [
                'id' => 4,
                'service_id' => 1,
                'name' => 'Sedan',
                'icon_name' => '219141420200710.png',
                'cost_for_km' => 15.00,
                'weight_limit' => Null,
                'width_limit' => Null,
                'height_limit' => Null,
                'dimension_limit' => Null,
                'base_fare' => 75.00,
                'time_fare' => 2.00,
                'min_fare_amount' => 150.00,
                'status' => 1
            ],
            [
                'id' => 5,
                'service_id' => 3,
                'name' => 'Cruiser',
                'icon_name' => '58451120230606.png',
                'cost_for_km' => 12.00,
                'weight_limit' => 0.00,
                'width_limit' => 0.00,
                'height_limit' => 0.00,
                'dimension_limit' => 0.00,
                'base_fare' => 0.00,
                'time_fare' => 0.00,
                'min_fare_amount' => 0.00,
                'status' => 1
            ],
            [
                'id' => 6,
                'service_id' => 3,
                'name' => 'Sports',
                'icon_name' => '33101220230606.png',
                'cost_for_km' => 14.00,
                'weight_limit' => 0.00,
                'width_limit' => 0.00,
                'height_limit' => 0.00,
                'dimension_limit' => 0.00,
                'base_fare' => 0.00,
                'time_fare' => 0.00,
                'min_fare_amount' => 0.00,
                'status' => 1
            ],
            [
                'id' => 7,
                'service_id' => 4,
                'name' => 'Van',
                'icon_name' => '21400920242309.png',
                'cost_for_km' => 0.00,
                'weight_limit' => 0.00,
                'width_limit' => 0.00,
                'height_limit' => 0.00,
                'dimension_limit' => 0.00,
                'base_fare' => 0.00,
                'time_fare' => 0.00,
                'min_fare_amount' => 0.00,
                'status' => 1
            ],
            [
                'id' => 8,
                'service_id' => 5,
                'name' => 'Auto Rickshaw',
                'icon_name' => '43390720241709.png',
                'cost_for_km' => 0.00,
                'weight_limit' => 0.00,
                'width_limit' => 0.00,
                'height_limit' => 0.00,
                'dimension_limit' => 0.00,
                'base_fare' => 0.00,
                'time_fare' => 0.00,
                'min_fare_amount' => 0.00,
                'status' => 1
            ],
            [
                'id' => 9,
                'service_id' => 5,
                'name' => 'E-Rickshaw',
                'icon_name' => '46360720241709.png',
                'cost_for_km' => 0.00,
                'weight_limit' => 0.00,
                'width_limit' => 0.00,
                'height_limit' => 0.00,
                'dimension_limit' => 0.00,
                'base_fare' => 0.00,
                'time_fare' => 0.00,
                'min_fare_amount' => 0.00,
                'status' => 1
            ],
        ];
        /*
        | upsert
        |--------------------------------------------------------------------------
        | We are using upsert here as it functions to either insert or update records efficiently.
        | If a record already exists, it updates it; if not, it inserts a new record.
        | This operation compares records using a unique key and supports handling multiple records in a single operation.
        */
        DB::table('transport_vehicle_type')->upsert(
            $transport_vehicle_type_record,
            ['id'], // Unique column to determine if a row exists
            ['service_id', 'name', 'icon_name','cost_for_km','weight_limit','width_limit','height_limit','dimension_limit','base_fare','time_fare','min_fare_amount','status']
        );
    }
}
