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
        if (!Schema::hasTable('vehicle_services')) {
            Schema::create('vehicle_services', function (Blueprint $table) {
                $table->id();
                $table->string('name', 130)->nullable();
                $table->string('pt_name', 191)->nullable();
                $table->string('es_name', 191)->nullable();
                $table->string('fr_name', 191)->nullable();
                $table->string('it_name', 191)->nullable();
                $table->string('icon_name', 191)->nullable();
                $table->double('cost_for_km');
                $table->double('max_bargain_percent');
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
        Schema::dropIfExists('vehicle_services');
    }
};
