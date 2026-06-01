<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transport_driver_details') || !Schema::hasTable('transport_vehicle_type')) {
            return;
        }

        $driverIds = DB::table('transport_driver_details as tdd')
            ->join('transport_vehicle_type as tvt', 'tvt.id', '=', 'tdd.vehicle_type_id')
            ->whereIn('tvt.service_id', [1, 3, 5])
            ->where('tdd.accept_transport', 0)
            ->pluck('tdd.id');

        if ($driverIds->isNotEmpty()) {
            DB::table('transport_driver_details')
                ->whereIn('id', $driverIds)
                ->update(['accept_transport' => 1, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Data fix — no rollback.
    }
};
