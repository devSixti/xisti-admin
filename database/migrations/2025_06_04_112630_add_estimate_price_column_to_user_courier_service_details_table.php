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
        Schema::table('user_courier_service_details', function (Blueprint $table) {
            //
            $table->double('estimate_price')->default(0.00)->after('item_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_courier_service_details', function (Blueprint $table) {
            //
            $table->dropColumn('estimate_price');
        });
    }
};
