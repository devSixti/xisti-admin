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
        // Check if the table does not exist then create
        if (!Schema::hasTable('admin_area_list')) {
            Schema::create('admin_area_list', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable()->comment('City Name');
                $table->text('latitude')->nullable()->comment('City latitude');
                $table->text('longitude')->nullable()->comment('City longitude');
                $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_area_list');
    }
};
