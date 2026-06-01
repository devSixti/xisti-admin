<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->tinyInteger('is_way_point')->default(0)->comment('1= multi stop')->after('status');
            $table->tinyInteger('way_point_status')->default(0)->comment('1->first waypoint complete, 2->second waypoint complete, 3->third waypoint complete')->after('is_way_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->dropColumn('is_way_point');
            $table->dropColumn('way_point_status');
        });
    }
};
