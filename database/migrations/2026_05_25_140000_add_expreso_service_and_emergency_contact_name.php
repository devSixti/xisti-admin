<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'emergency_contact_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('emergency_contact_name', 120)->nullable()->after('emergency_contact');
            });
        }

        if (!Schema::hasTable('vehicle_services')) {
            return;
        }

        $exists = DB::table('vehicle_services')->where('service_mode', 'expreso')->exists();
        if ($exists) {
            return;
        }

        $now = now();
        $row = [
            'name' => 'Expreso',
            'service_mode' => 'expreso',
            'status' => 1,
            'cost_for_km' => 2.5,
            'vehicle_service_description' => 'Viajes de larga distancia',
            'icon_name' => '',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        if (Schema::hasColumn('vehicle_services', 'es_name')) {
            $row['es_name'] = 'Expreso';
        }
        if (Schema::hasColumn('vehicle_services', 'en_name')) {
            $row['en_name'] = 'Express';
            $row['fr_name'] = 'Express';
            $row['it_name'] = 'Express';
            $row['pt_name'] = 'Expresso';
        }
        if (Schema::hasColumn('vehicle_services', 'display_order')) {
            $row['display_order'] = 25;
        }
        if (Schema::hasColumn('vehicle_services', 'max_bargain_percent')) {
            $row['max_bargain_percent'] = 10;
        }
        DB::table('vehicle_services')->insert($row);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'emergency_contact_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('emergency_contact_name');
            });
        }

        if (Schema::hasTable('vehicle_services')) {
            DB::table('vehicle_services')->where('service_mode', 'expreso')->delete();
        }
    }
};
