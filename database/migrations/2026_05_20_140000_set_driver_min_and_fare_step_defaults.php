<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        $updates = [];
        if (Schema::hasColumn('general_settings', 'driver_min_amount')) {
            $updates['driver_min_amount'] = 13000;
        }
        if (Schema::hasColumn('general_settings', 'fare_negotiation_step')) {
            $updates['fare_negotiation_step'] = 500;
        }
        if ($updates !== []) {
            DB::table('general_settings')->update($updates);
        }
    }

    public function down(): void
    {
        // No rollback: production tuning for Colombia MVP.
    }
};
