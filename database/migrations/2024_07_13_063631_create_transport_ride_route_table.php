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
        if (!Schema::hasTable('transport_ride_route')) {
            Schema::create('transport_ride_route', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('ride_id');
                $table->longText('ride_route');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_ride_route');
    }
};
