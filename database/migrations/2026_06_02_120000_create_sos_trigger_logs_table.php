<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sos_trigger_logs')) {
            return;
        }

        Schema::create('sos_trigger_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('ride_id')->nullable()->index();
            $table->string('user_role', 32)->default('passenger');
            $table->string('contact_name', 191)->nullable();
            $table->string('country_code', 16)->nullable();
            $table->string('contact_number', 32)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('product', 16)->default('XISTI');
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sos_trigger_logs');
    }
};
