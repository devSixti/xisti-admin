<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_services') || ! Schema::hasColumn('vehicle_services', 'display_order')) {
            return;
        }

        DB::table('vehicle_services')->where('id', 3)->update(['display_order' => 1, 'updated_at' => now()]); // Moto
        DB::table('vehicle_services')->where('id', 1)->update(['display_order' => 2, 'updated_at' => now()]); // Carro
        DB::table('vehicle_services')->where('id', 5)->update(['display_order' => 3, 'updated_at' => now()]); // Motoraton
    }

    public function down(): void
    {
        // Non-destructive.
    }
};

