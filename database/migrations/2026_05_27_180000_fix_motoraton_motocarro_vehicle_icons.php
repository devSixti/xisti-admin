<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Viajes (id 5): Motoratón verde — compatible con app 1.0.1 en tiendas.
 * Envíos: motocarro.png se asigna solo en DeliveryVehicleHelper (no en icon_name de id 5).
 * Encomiendas: sin icono propio; la app usa delivery_vehicle_options.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_services')) {
            return;
        }

        DB::table('vehicle_services')
            ->where('id', 5)
            ->update(['icon_name' => '27531520260705.png', 'updated_at' => now()]);

        DB::table('vehicle_services')
            ->where('service_mode', 'encomiendas')
            ->update(['icon_name' => '', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
