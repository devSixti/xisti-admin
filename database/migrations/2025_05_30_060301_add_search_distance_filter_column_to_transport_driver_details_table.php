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
        Schema::table('transport_driver_details', function (Blueprint $table) {
            //
            $table->double('search_distance_filter')->default(0)->after('doc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            //
            $table->dropColumn('search_distance_filter');
        });
    }
};
