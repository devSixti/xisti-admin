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
            $table->string('courier_services')->nullable()->after('max_bargain_percent');
            $table->string('vehicle_service_icon')->nullable()->after('icon_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_services', function (Blueprint $table) {
            //
        });
    }
};
