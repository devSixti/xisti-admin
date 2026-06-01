<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_settings') || ! Schema::hasColumn('general_settings', 'fare_negotiation_step')) {
            return;
        }

        DB::table('general_settings')->update(['fare_negotiation_step' => 500]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings') || ! Schema::hasColumn('general_settings', 'fare_negotiation_step')) {
            return;
        }

        DB::table('general_settings')->update(['fare_negotiation_step' => 1000]);
    }
};
