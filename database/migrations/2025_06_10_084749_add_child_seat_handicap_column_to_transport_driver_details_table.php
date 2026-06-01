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
            $table->boolean('handicap')->default(0)->comment('0 = no , 1 = handicap accessibility')->after('search_distance_filter');
            $table->boolean('child_seat')->default(0)->after('handicap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            //
            $table->dropColumn('handicap');
            $table->dropColumn('child_seat');
        });
    }
};
