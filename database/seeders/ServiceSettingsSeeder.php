<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $service_settings_record =[
                [
                    'id' => 1,
                    'provider_accept_timeout' => Null,
                    'driver_timeout' => 30,
                    'provider_search_radius' => 50,
                    'tax' => Null,
                    'admin_commission' => (float) config('xisti.default_commission_percent', 8),
                    'admin_hail_commission' => (float) config('xisti.default_commission_percent', 8),
                    'cancel_charge' => Null,
                    'ride_expiry' => 30,
                    'nearest_ride_popup' => 5.00,
                    'status' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]
            ];
        /*
        | upsert
        |--------------------------------------------------------------------------
        | We are using upsert here as it functions to either insert or update records efficiently.
        | If a record already exists, it updates it; if not, it inserts a new record.
        | This operation compares records using a unique key and supports handling multiple records in a single operation.
        */
        DB::table('service_settings')->upsert(
            $service_settings_record,
            ['id'], // Unique column to determine if a row exists
            ['provider_accept_timeout', 'driver_timeout', 'provider_search_radius', 'tax', 'admin_commission', 'admin_hail_commission', 'cancel_charge', 'ride_expiry','nearest_ride_popup', 'status', 'created_at', 'updated_at']
        );
    }
}
