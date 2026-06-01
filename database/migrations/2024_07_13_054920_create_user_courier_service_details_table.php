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
        if (!Schema::hasTable('user_courier_service_details')) {
            Schema::create('user_courier_service_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('ride_id');
                $table->string('recipient_name', 60);
                $table->string('recipient_contact_number', 20);
                $table->text('item_description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_courier_service_details');
    }
};
