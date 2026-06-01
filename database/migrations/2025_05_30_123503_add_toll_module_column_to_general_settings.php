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
        Schema::table('general_settings', function (Blueprint $table) {
            //
            $table->tinyInteger('is_toll_module')->default(0)->comment('0->off toll module, 1->final toll charge given by the driver, 2->No. of tolls given by the driver & fixed charge/toll is from the admin')->after('driver_min_amount');
            $table->float('charge_per_toll')->default(0.00)->comment('Toll charge is for no. of tolls given by driver')->after('is_toll_module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            //
            $table->dropColumn('is_toll_module');
            $table->dropColumn('charge_per_toll');
        });
    }
};
