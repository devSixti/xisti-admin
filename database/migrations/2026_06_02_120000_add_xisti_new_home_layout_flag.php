<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'enable_xisti_new_home_layout')) {
                $table->unsignedTinyInteger('enable_xisti_new_home_layout')->default(1)->after('require_courier_package_dimensions_mobile');
            }
        });

        if (Schema::hasTable('general_settings') && Schema::hasColumn('general_settings', 'enable_xisti_new_home_layout')) {
            DB::table('general_settings')->update(['enable_xisti_new_home_layout' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'enable_xisti_new_home_layout')) {
                $table->dropColumn('enable_xisti_new_home_layout');
            }
        });
    }
};
