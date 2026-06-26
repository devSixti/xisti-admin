<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_commission_rates')) {
            Schema::create('vehicle_commission_rates', function (Blueprint $table) {
                $table->id();
                $table->string('variant_key', 64)->unique();
                $table->string('label', 120);
                $table->unsignedInteger('vehicle_service_id')->nullable();
                $table->float('commission_percent')->default(8);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_commission_rates');
    }
};
