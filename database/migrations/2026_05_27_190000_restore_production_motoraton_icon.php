<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Restaura el icono de Motoratón que ya estaba en producción (subida admin),
 * no el asset generado motoraton.png.
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
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
