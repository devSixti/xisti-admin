<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_driver_details', 'accept_encomiendas')) {
                $table->tinyInteger('accept_encomiendas')->default(0)->after('accept_delivery');
            }
        });

        if (Schema::hasColumn('transport_driver_details', 'accept_encomiendas')
            && Schema::hasColumn('transport_driver_details', 'accept_delivery')) {
            DB::table('transport_driver_details')
                ->update([
                    'accept_encomiendas' => DB::raw('accept_delivery'),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('transport_driver_details', function (Blueprint $table) {
            if (Schema::hasColumn('transport_driver_details', 'accept_encomiendas')) {
                $table->dropColumn('accept_encomiendas');
            }
        });
    }
};

