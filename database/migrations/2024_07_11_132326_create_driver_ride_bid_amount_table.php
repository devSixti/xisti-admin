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
        if (!Schema::hasTable('driver_ride_bid_amount')) {
            Schema::create('driver_ride_bid_amount', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('driver_id');
                $table->bigInteger('user_id');
                $table->bigInteger('ride_id');
                $table->bigInteger('vehicle_type_id');
                $table->double('offered_price')->nullable();
                $table->tinyInteger('status')->nullable();
                $table->dateTime('bidding_time')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_ride_bid_amount');
    }
};
