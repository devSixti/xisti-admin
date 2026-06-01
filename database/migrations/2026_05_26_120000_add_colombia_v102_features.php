<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_ride_booking') && !Schema::hasColumn('user_ride_booking', 'destination_payment_method')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->string('destination_payment_method', 32)->nullable()->after('payment_type');
            });
        }

        if (Schema::hasTable('transport_driver_details') && !Schema::hasColumn('transport_driver_details', 'is_taxi')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->unsignedTinyInteger('is_taxi')->default(0)->after('handicap');
            });
        }

        if (Schema::hasTable('user_courier_service_details')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                if (!Schema::hasColumn('user_courier_service_details', 'package_weight_kg')) {
                    $table->decimal('package_weight_kg', 8, 2)->nullable()->after('item_description');
                }
                if (!Schema::hasColumn('user_courier_service_details', 'package_height_cm')) {
                    $table->decimal('package_height_cm', 8, 2)->nullable()->after('package_weight_kg');
                }
                if (!Schema::hasColumn('user_courier_service_details', 'package_width_cm')) {
                    $table->decimal('package_width_cm', 8, 2)->nullable()->after('package_height_cm');
                }
                if (!Schema::hasColumn('user_courier_service_details', 'package_length_cm')) {
                    $table->decimal('package_length_cm', 8, 2)->nullable()->after('package_width_cm');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_ride_booking', 'destination_payment_method')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->dropColumn('destination_payment_method');
            });
        }
        if (Schema::hasColumn('transport_driver_details', 'is_taxi')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->dropColumn('is_taxi');
            });
        }
        foreach (['package_weight_kg', 'package_height_cm', 'package_width_cm', 'package_length_cm'] as $col) {
            if (Schema::hasColumn('user_courier_service_details', $col)) {
                Schema::table('user_courier_service_details', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
