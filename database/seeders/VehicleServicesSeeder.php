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
                'name' => 'Carro',
                'pt_name' => 'Carro',
                'es_name' => 'Carro',
                'fr_name' => 'Voiture',
                'it_name' => 'Auto',
                'icon_name' => '00031020241208.png',
                'cost_for_km' => 2200.00,
                'max_bargain_percent' => 10.00,
                'max_offer_percent' => 10.00,
                'min_fare' => 10000.00,
                'time_fare' => 50.00,
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
                'cost_for_km' => 900.00,
                'min_fare' => 5000.00,
                'time_fare' => 30.00,
                'max_bargain_percent' => 10.00,
                'max_offer_percent' => 10.00,
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
                'min_fare' => 8000.00,
                'time_fare' => 25.00,
                'max_bargain_percent' => 8.00,
                'max_offer_percent' => 8.00,
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
                'min_fare' => 6000.00,
                'time_fare' => 25.00,
                'max_bargain_percent' => 5.00,
                'max_offer_percent' => 5.00,
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
                'min_fare',
                'time_fare',
                'max_bargain_percent',
                'max_offer_percent',
                'status',
                'service_mode',
                'display_order',
            ]
        );
    }
}
