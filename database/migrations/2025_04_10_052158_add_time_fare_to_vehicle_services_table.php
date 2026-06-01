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
        Schema::table('vehicle_services', function (Blueprint $table) {
            $table->double('time_fare')->default(0.00)->after('cost_for_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_services', function (Blueprint $table) {
            $table->dropColumn(['time_fare']);
        });
    }
};
