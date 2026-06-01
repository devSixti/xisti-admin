<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_services', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_services', 'service_mode')) {
                $table->string('service_mode', 20)->default('transport')->after('status');
            }
            if (!Schema::hasColumn('vehicle_services', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0)->after('service_mode');
            }
        });

        DB::table('vehicle_services')->where('id', 4)->update([
            'service_mode' => 'delivery',
            'display_order' => 1,
        ]);
        DB::table('vehicle_services')->whereIn('id', [1, 3, 5])->update(['service_mode' => 'transport']);
        DB::table('vehicle_services')->where('id', 1)->update(['display_order' => 1]);
        DB::table('vehicle_services')->where('id', 3)->update(['display_order' => 2]);
        DB::table('vehicle_services')->where('id', 5)->update(['display_order' => 3]);
    }

    public function down(): void
    {
        Schema::table('vehicle_services', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_services', 'display_order')) {
                $table->dropColumn('display_order');
            }
            if (Schema::hasColumn('vehicle_services', 'service_mode')) {
                $table->dropColumn('service_mode');
            }
        });
    }
};
