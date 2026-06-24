<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_ride_booking')
            && ! Schema::hasColumn('user_ride_booking', 'delivery_variant')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->string('delivery_variant', 64)->nullable()->after('vehicle_service_id');
            });
        }

        if (Schema::hasTable('transport_driver_details')
            && ! Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->string('delivery_variant', 64)->nullable()->after('vehicle_type_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_ride_booking', 'delivery_variant')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->dropColumn('delivery_variant');
            });
        }

        if (Schema::hasColumn('transport_driver_details', 'delivery_variant')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->dropColumn('delivery_variant');
            });
        }
    }
};
