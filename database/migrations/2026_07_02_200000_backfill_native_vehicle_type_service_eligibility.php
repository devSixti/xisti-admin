<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_type_service_eligibility') || ! Schema::hasTable('transport_vehicle_type')) {
            return;
        }

        $types = DB::table('transport_vehicle_type')->select('id', 'service_id')->get();
        foreach ($types as $type) {
            $serviceId = (int) ($type->service_id ?? 0);
            if ($serviceId <= 0) {
                continue;
            }
            DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                ['vehicle_type_id' => (int) $type->id, 'service_id' => $serviceId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Data backfill — no rollback.
    }
};
