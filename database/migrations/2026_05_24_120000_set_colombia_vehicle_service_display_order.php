<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Canonical passenger-app order (Colombia):
     * Transport tab: Moto (3) → Moto-ratón (5) → Carro (1)
     * Delivery tab: Envíos (4)
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
            4 => ['service_mode' => 'delivery', 'display_order' => 1],
        ];

        foreach ($updates as $id => $values) {
            DB::table('vehicle_services')->where('id', $id)->update($values);
        }
    }

    public function down(): void
    {
        // Non-destructive: leave admin-configured order as-is on rollback.
    }
};
