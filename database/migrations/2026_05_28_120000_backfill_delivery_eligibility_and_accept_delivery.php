<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_type_service_eligibility') && Schema::hasTable('transport_vehicle_type')) {
            $transportTypeIds = DB::table('transport_vehicle_type')
                ->whereIn('service_id', [1, 3, 5])
                ->pluck('id');
            foreach ($transportTypeIds as $typeId) {
                DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                    ['vehicle_type_id' => $typeId, 'service_id' => 4],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        if (Schema::hasTable('transport_driver_details') && Schema::hasTable('transport_vehicle_type')) {
            $driverIds = DB::table('transport_driver_details as tdd')
                ->join('transport_vehicle_type as tvt', 'tvt.id', '=', 'tdd.vehicle_type_id')
                ->whereIn('tvt.service_id', [1, 3, 5])
                ->where(function ($q) {
                    $q->where('tdd.accept_delivery', 0)->orWhereNull('tdd.accept_delivery');
                })
                ->pluck('tdd.id');

            if ($driverIds->isNotEmpty()) {
                DB::table('transport_driver_details')
                    ->whereIn('id', $driverIds)
                    ->update(['accept_delivery' => 1, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Data backfill — no rollback.
    }
};
