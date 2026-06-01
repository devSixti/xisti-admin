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
            $table->double('max_bargain_amt')->default(0.00)->after('min_bargain_amt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->dropColumn('max_bargain_amt');
        });
    }
};
