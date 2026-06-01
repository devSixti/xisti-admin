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
        if (!Schema::hasTable('transport_driver_details')) {
            Schema::create('transport_driver_details', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->default(0);
                $table->unsignedInteger('vehicle_type_id');
                $table->integer('total_request')->default(0);
                $table->integer('total_completed')->default(0);
                $table->integer('total_cancelled')->default(0);
                $table->double('rating')->default(0.00);
                $table->string('current_lat', 30)->nullable();
                $table->string('current_long', 30)->nullable();
                $table->datetime('last_online_date_time')->useCurrent();
                $table->string('vehicle_company', 20);
                $table->string('plat_no', 20);
                $table->mediumInteger('model_year');
                $table->string('model_name', 20);
                $table->tinyInteger('availability_ride_status')->default(1)->comment('1 = available , 2 = accept, 3 enroute, 4= arrived(pickup) , 5 =start');
                $table->string('vehicle_color', 20)->nullable();
                $table->string('vehicle_image', 191)->nullable();
                $table->integer('no_of_seat')->default(0)->comment('0 = not required');
                $table->integer('doc_status')->default(0)->comment('0=doc pending, 1=doc completed');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_driver_details');
    }
};
