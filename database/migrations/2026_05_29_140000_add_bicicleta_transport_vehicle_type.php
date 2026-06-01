<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transport_vehicle_type')) {
            return;
        }

        $exists = DB::table('transport_vehicle_type')
            ->where('service_id', 3)
            ->whereRaw('LOWER(name) LIKE ?', ['%bicicleta%'])
            ->exists();

        if ($exists) {
            return;
        }

        $nextId = (int) (DB::table('transport_vehicle_type')->max('id') ?? 0) + 1;

        DB::table('transport_vehicle_type')->insert([
            'id' => $nextId,
            'service_id' => 3,
            'name' => 'Bicicleta',
            'icon_name' => '',
            'cost_for_km' => 0,
            'weight_limit' => 0,
            'width_limit' => 0,
            'height_limit' => 0,
            'dimension_limit' => 0,
            'base_fare' => 0,
            'time_fare' => 0,
            'min_fare_amount' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('vehicle_type_service_eligibility')) {
            $hasDelivery = DB::table('vehicle_type_service_eligibility')
                ->where('vehicle_type_id', $nextId)
                ->where('service_id', 4)
                ->exists();
            if (! $hasDelivery) {
                DB::table('vehicle_type_service_eligibility')->insert([
                    'vehicle_type_id' => $nextId,
                    'service_id' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('transport_vehicle_type')) {
            return;
        }

        DB::table('transport_vehicle_type')
            ->where('service_id', 3)
            ->where('name', 'Bicicleta')
            ->delete();
    }
};
