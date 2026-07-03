<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fare_pricing_rules')) {
            return;
        }

        Schema::create('fare_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('rule_type', 32)->comment('peak,weather,demand,weekday,holiday,driver_offer,occupancy,special');
            $table->decimal('multiplier', 8, 4)->default(1);
            $table->json('conditions')->nullable();
            $table->unsignedInteger('priority')->default(100);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fare_pricing_rules');
    }
};
