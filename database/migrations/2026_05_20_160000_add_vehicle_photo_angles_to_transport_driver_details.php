<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_driver_details', 'vehicle_image_front')) {
                $table->string('vehicle_image_front', 191)->nullable()->after('vehicle_image');
            }
            if (! Schema::hasColumn('transport_driver_details', 'vehicle_image_side')) {
                $table->string('vehicle_image_side', 191)->nullable()->after('vehicle_image_front');
            }
            if (! Schema::hasColumn('transport_driver_details', 'vehicle_image_rear')) {
                $table->string('vehicle_image_rear', 191)->nullable()->after('vehicle_image_side');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            foreach (['vehicle_image_rear', 'vehicle_image_side', 'vehicle_image_front'] as $col) {
                if (Schema::hasColumn('transport_driver_details', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
