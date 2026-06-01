<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Global passenger home order (flat services list — mobile v1.0.1+):
     * Moto (3) → Moto-ratón (5) → Carro (1) → Envíos (4)
     */
    public function up(): void
    {
        if (!Schema::hasColumn('vehicle_services', 'display_order')) {
            return;
        }

        $updates = [
            3 => ['service_mode' => 'transport', 'display_order' => 1],
            5 => ['service_mode' => 'transport', 'display_order' => 2],
            1 => ['service_mode' => 'transport', 'display_order' => 3],
            4 => ['service_mode' => 'delivery', 'display_order' => 4],
        ];

        foreach ($updates as $id => $values) {
            DB::table('vehicle_services')->where('id', $id)->update($values);
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('vehicle_services', 'display_order')) {
            return;
        }

        $updates = [
            3 => ['display_order' => 1],
            5 => ['display_order' => 2],
            1 => ['display_order' => 3],
            4 => ['display_order' => 1],
        ];

        foreach ($updates as $id => $values) {
            DB::table('vehicle_services')->where('id', $id)->update($values);
        }
    }
};
