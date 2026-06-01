<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_courier_service_details')
            && !Schema::hasColumn('user_courier_service_details', 'errand_type')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->string('errand_type', 20)->nullable()->default(null)->after('ride_id');
            });
        }

        if (!Schema::hasTable('vehicle_services')) {
            return;
        }

        if (!DB::table('vehicle_services')->where('service_mode', 'encomiendas')->exists()) {
            $now = now();
            $row = [
                'name' => 'Encomiendas',
                'service_mode' => 'encomiendas',
                'status' => 1,
                'cost_for_km' => 2.0,
                'vehicle_service_description' => 'Compras y entregas por encargo',
                'icon_name' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (Schema::hasColumn('vehicle_services', 'es_name')) {
                $row['es_name'] = 'Encomiendas';
            }
            if (Schema::hasColumn('vehicle_services', 'en_name')) {
                $row['en_name'] = 'Errands';
                $row['fr_name'] = 'Courses';
                $row['it_name'] = 'Commissioni';
                $row['pt_name'] = 'Encomendas';
            }
            if (Schema::hasColumn('vehicle_services', 'display_order')) {
                $row['display_order'] = 26;
            }
            if (Schema::hasColumn('vehicle_services', 'max_bargain_percent')) {
                $row['max_bargain_percent'] = 15;
            }
            DB::table('vehicle_services')->insert($row);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_courier_service_details', 'errand_type')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->dropColumn('errand_type');
            });
        }

        if (Schema::hasTable('vehicle_services')) {
            DB::table('vehicle_services')->where('service_mode', 'encomiendas')->delete();
        }
    }
};
