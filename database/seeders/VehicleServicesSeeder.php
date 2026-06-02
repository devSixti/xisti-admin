<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleServicesSeeder extends Seeder
{
    public function run(): void
    {
        $vehicle_services_record = [
            [
                'id' => 1,
                'name' => 'Taxi',
                'pt_name' => 'Táxi',
                'es_name' => 'Taxi',
                'fr_name' => 'Taxi',
                'it_name' => 'Taxi',
                'icon_name' => '00031020241208.png',
                'cost_for_km' => 22.00,
                'max_bargain_percent' => 10.00,
                'status' => 1,
                'service_mode' => 'transport',
                'display_order' => 3,
            ],
            [
                'id' => 3,
                'name' => 'Moto',
                'pt_name' => 'Moto',
                'es_name' => 'Moto',
                'fr_name' => 'Moto',
                'it_name' => 'Moto',
                'icon_name' => '09031020241208.png',
                'cost_for_km' => 8.00,
                'max_bargain_percent' => 10.00,
                'status' => 1,
                'service_mode' => 'transport',
                'display_order' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Courier',
                'pt_name' => 'Correio',
                'es_name' => 'Mensajero',
                'fr_name' => 'Coursier',
                'it_name' => 'Corriere',
                'icon_name' => '16031020241208.png',
                'cost_for_km' => 10.00,
                'max_bargain_percent' => 8.00,
                'status' => 1,
                'service_mode' => 'delivery',
                'display_order' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Rickshaw',
                'pt_name' => 'Riquixá',
                'es_name' => 'Bicitaxi',
                'fr_name' => 'Pousse-pousse',
                'it_name' => 'Risciò',
                'icon_name' => '24031020241208.png',
                'cost_for_km' => 15.00,
                'max_bargain_percent' => 5.00,
                'status' => 0,
                'service_mode' => 'transport',
                'display_order' => 99,
            ],
        ];

        DB::table('vehicle_services')->upsert(
            $vehicle_services_record,
            ['id'],
            [
                'name',
                'pt_name',
                'es_name',
                'fr_name',
                'it_name',
                'icon_name',
                'cost_for_km',
                'max_bargain_percent',
                'status',
                'service_mode',
                'display_order',
            ]
        );
    }
}
