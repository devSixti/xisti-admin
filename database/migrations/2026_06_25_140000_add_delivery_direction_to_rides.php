<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_ride_booking')
            && ! Schema::hasColumn('user_ride_booking', 'delivery_direction')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->string('delivery_direction', 16)->nullable();
            });
        }

        if (Schema::hasTable('user_courier_service_details')
            && ! Schema::hasColumn('user_courier_service_details', 'delivery_direction')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->string('delivery_direction', 16)->nullable();
            });
        }

        if (Schema::hasTable('user_courier_service_details')
            && ! Schema::hasColumn('user_courier_service_details', 'sender_name')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->string('sender_name', 80)->nullable();
                $table->string('sender_contact_number', 32)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_ride_booking', 'delivery_direction')) {
            Schema::table('user_ride_booking', function (Blueprint $table) {
                $table->dropColumn('delivery_direction');
            });
        }

        foreach (['sender_contact_number', 'sender_name', 'delivery_direction'] as $col) {
            if (Schema::hasColumn('user_courier_service_details', $col)) {
                Schema::table('user_courier_service_details', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
