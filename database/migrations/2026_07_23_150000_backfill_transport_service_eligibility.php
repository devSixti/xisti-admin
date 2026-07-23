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

        $rows = DB::table('transport_vehicle_type')
            ->where('status', 1)
            ->where('service_id', '>', 0)
            ->where('service_id', '!=', 4)
            ->get(['id as vehicle_type_id', 'service_id']);

        foreach ($rows as $row) {
            DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                [
                    'vehicle_type_id' => (int) $row->vehicle_type_id,
                    'service_id' => (int) $row->service_id,
                ],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
