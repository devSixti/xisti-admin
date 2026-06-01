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
            if (! Schema::hasColumn('general_settings', 'enable_expreso_mobile')) {
                $table->unsignedTinyInteger('enable_expreso_mobile')->default(0)->after('driver_cancel_until_status');
            }
            if (! Schema::hasColumn('general_settings', 'enable_encomiendas_mobile')) {
                $table->unsignedTinyInteger('enable_encomiendas_mobile')->default(0)->after('enable_expreso_mobile');
            }
            if (! Schema::hasColumn('general_settings', 'require_courier_package_dimensions_mobile')) {
                $table->unsignedTinyInteger('require_courier_package_dimensions_mobile')->default(0)->after('enable_encomiendas_mobile');
            }
        });

        if (Schema::hasTable('general_settings')) {
            DB::table('general_settings')->update([
                'enable_expreso_mobile' => 0,
                'enable_encomiendas_mobile' => 0,
                'require_courier_package_dimensions_mobile' => 0,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            foreach (['enable_expreso_mobile', 'enable_encomiendas_mobile', 'require_courier_package_dimensions_mobile'] as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
