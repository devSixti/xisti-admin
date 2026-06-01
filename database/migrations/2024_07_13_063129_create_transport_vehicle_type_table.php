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
        if (!Schema::hasTable('transport_vehicle_type')) {
            Schema::create('transport_vehicle_type', function (Blueprint $table) {
                $table->id();
                $table->integer('service_id');
                $table->string('name', 20);
                $table->string('icon_name', 25);
                $table->double('cost_for_km');
                $table->double('weight_limit')->default(0.00);
                $table->double('width_limit')->default(0.00);
                $table->double('height_limit')->default(0.00);
                $table->string('dimension_limit', 20)->nullable();
                $table->double('base_fare')->default(0.00);
                $table->double('time_fare')->default(0.00);
                $table->double('min_fare_amount')->default(0.00);
                $table->tinyInteger('status')->default(1)->comment('1=activate, 0=deactivate');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_vehicle_type');
    }
};
