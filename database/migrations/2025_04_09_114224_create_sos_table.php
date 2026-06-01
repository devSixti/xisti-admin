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
        if (!Schema::hasTable('sos')) {
            Schema::create('sos', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('pt_name', 255)->nullable();
                $table->string('es_name', 255)->nullable();
                $table->string('fr_name', 255)->nullable();
                $table->string('it_name', 255)->nullable();
                $table->string('country_code', 10)->nullable();
                $table->unsignedBigInteger('contact_number');
                $table->boolean('status')->default(1)->comment('1=active, 0=block');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos');
    }
};
