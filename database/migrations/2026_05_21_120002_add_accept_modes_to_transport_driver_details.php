<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            if (!Schema::hasColumn('transport_driver_details', 'accept_transport')) {
                $table->tinyInteger('accept_transport')->default(1)->after('search_distance_filter');
            }
            if (!Schema::hasColumn('transport_driver_details', 'accept_delivery')) {
                $table->tinyInteger('accept_delivery')->default(0)->after('accept_transport');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            if (Schema::hasColumn('transport_driver_details', 'accept_delivery')) {
                $table->dropColumn('accept_delivery');
            }
            if (Schema::hasColumn('transport_driver_details', 'accept_transport')) {
                $table->dropColumn('accept_transport');
            }
        });
    }
};
