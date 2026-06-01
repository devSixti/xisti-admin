<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_type_service_eligibility')) {
            Schema::create('vehicle_type_service_eligibility', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('vehicle_type_id');
                $table->unsignedInteger('service_id');
                $table->timestamps();
                $table->unique(['vehicle_type_id', 'service_id'], 'vt_service_unique');
            });
        }

        $types = DB::table('transport_vehicle_type')->select('id', 'service_id')->get();
        foreach ($types as $type) {
            DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                ['vehicle_type_id' => $type->id, 'service_id' => $type->service_id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_type_service_eligibility');
    }
};
