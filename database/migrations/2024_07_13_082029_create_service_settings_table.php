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
        if (!Schema::hasTable('service_settings')) {
            Schema::create('service_settings', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('provider_accept_timeout')->nullable();
                $table->integer('driver_timeout')->default(20);
                $table->integer('provider_search_radius')->nullable();
                $table->float('tax')->nullable();
                $table->float('admin_commission')->nullable();
                $table->float('cancel_charge')->nullable();
                $table->integer('ride_expiry')->nullable();
                $table->double('nearest_ride_popup')->default(0.00);
                $table->tinyInteger('status')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_settings');
    }
};
