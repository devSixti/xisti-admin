<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_driver_details')
            && !Schema::hasColumn('transport_driver_details', 'technical_inspection_expiry')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->date('technical_inspection_expiry')->nullable()->after('model_year');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_driver_details')
            && Schema::hasColumn('transport_driver_details', 'technical_inspection_expiry')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->dropColumn('technical_inspection_expiry');
            });
        }
    }
};
