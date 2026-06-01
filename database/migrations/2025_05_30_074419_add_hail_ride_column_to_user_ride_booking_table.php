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
            $table->tinyInteger('is_hail')->default(0)->comment('0->normal ride, 1->hail ride')->after('user_refer_history_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->dropColumn('is_hail');
        });
    }
};
