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
        Schema::create('user_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('address_type')->default(3)->comment('1->home, 2->work, 3->other');
            $table->text('address');
            $table->string('lat_long', 191);
            $table->tinyInteger('status')->default(1)->comment('1=activate, 0=remove');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_address');
    }
};
