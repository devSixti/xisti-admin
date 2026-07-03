<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shared_ride_offers') && ! Schema::hasColumn('shared_ride_offers', 'fare_per_person')) {
            Schema::table('shared_ride_offers', function (Blueprint $table) {
                $table->decimal('fare_per_person', 12, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shared_ride_offers') && Schema::hasColumn('shared_ride_offers', 'fare_per_person')) {
            Schema::table('shared_ride_offers', function (Blueprint $table) {
                $table->dropColumn('fare_per_person');
            });
        }
    }
};
