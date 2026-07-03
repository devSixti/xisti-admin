<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shared_ride_offers') && ! Schema::hasColumn('shared_ride_offers', 'vehicle_variant')) {
            Schema::table('shared_ride_offers', function (Blueprint $table) {
                $table->string('vehicle_variant', 64)->nullable()->after('driver_vehicle_type_id');
            });
        }

        if (Schema::hasTable('shared_ride_passenger_searches') && ! Schema::hasColumn('shared_ride_passenger_searches', 'vehicle_variant')) {
            Schema::table('shared_ride_passenger_searches', function (Blueprint $table) {
                $table->string('vehicle_variant', 64)->nullable()->after('trip_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shared_ride_offers') && Schema::hasColumn('shared_ride_offers', 'vehicle_variant')) {
            Schema::table('shared_ride_offers', function (Blueprint $table) {
                $table->dropColumn('vehicle_variant');
            });
        }

        if (Schema::hasTable('shared_ride_passenger_searches') && Schema::hasColumn('shared_ride_passenger_searches', 'vehicle_variant')) {
            Schema::table('shared_ride_passenger_searches', function (Blueprint $table) {
                $table->dropColumn('vehicle_variant');
            });
        }
    }
};
