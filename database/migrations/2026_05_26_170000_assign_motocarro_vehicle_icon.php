<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_services')) {
            return;
        }

        // id 5 = Motoratón en viajes (icono subido en admin, p. ej. 27531520260705.png).
        DB::table('vehicle_services')
            ->where('id', 5)
            ->update(['icon_name' => '27531520260705.png', 'updated_at' => now()]);

        DB::table('vehicle_services')
            ->where('service_mode', 'expreso')
            ->where(function ($q) {
                $q->whereNull('icon_name')->orWhere('icon_name', '');
            })
            ->update(['icon_name' => 'expreso_bus.png', 'updated_at' => now()]);

        DB::table('vehicle_services')
            ->where('service_mode', 'encomiendas')
            ->update(['icon_name' => '', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
