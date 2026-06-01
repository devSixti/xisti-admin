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
            //
            $table->text('vehicle_service_description')->nullable()->after('courier_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_services', function (Blueprint $table) {
            //
            $table->dropColumn('vehicle_service_description');
        });
    }
};
