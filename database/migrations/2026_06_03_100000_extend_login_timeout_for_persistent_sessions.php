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

        DB::table('general_settings')
            ->where('login_timeout_time', '<', 525600)
            ->update(['login_timeout_time' => 525600]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        DB::table('general_settings')
            ->where('login_timeout_time', 525600)
            ->update(['login_timeout_time' => 10080]);
    }
};
