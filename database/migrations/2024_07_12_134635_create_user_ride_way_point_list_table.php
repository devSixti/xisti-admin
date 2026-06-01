<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table does not exist then create
        if (!Schema::hasTable('user_ride_way_point_list')) {
            Schema::create('user_ride_way_point_list', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('ride_id')->nullable();
                $table->string('way_point_1', 191);
                $table->string('lat_long_1', 191);
                $table->string('way_point_2', 191)->nullable();
                $table->string('lat_long_2', 191)->nullable();
                $table->string('way_point_3', 191)->nullable();
                $table->string('lat_long_3', 191)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ride_way_point_list');
    }
};
