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
        if (!Schema::hasTable('transport_driver_rating')) {
            Schema::create('transport_driver_rating', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('driver_id');
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('ride_id');
                $table->double('rating');
                $table->longText('comment')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_driver_rating');
    }
};
