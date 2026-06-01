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
        if (!Schema::hasTable('user_used_refer_history')) {
            Schema::create('user_used_refer_history', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('order_id')->nullable();
                $table->integer('service_cat_id')->default(0);
                $table->double('user_discount')->default(0.00);
                $table->tinyInteger('user_discount_type')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_used_refer_history');
    }
};
