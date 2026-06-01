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
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191);
                $table->string('pt_name', 191)->nullable();
                $table->string('es_name', 191)->nullable();
                $table->string('fr_name', 191)->nullable();
                $table->string('it_name', 191)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=Off,1=On');
                $table->longText('description')->nullable();
                $table->longText('pt_description')->nullable();
                $table->longText('es_description')->nullable();
                $table->longText('fr_description')->nullable();
                $table->longText('it_description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
