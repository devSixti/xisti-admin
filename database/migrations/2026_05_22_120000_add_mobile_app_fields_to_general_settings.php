<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'fare_negotiation_step')) {
                $table->unsignedInteger('fare_negotiation_step')->default(500)->after('driver_price_suggestion');
            }
            if (!Schema::hasColumn('general_settings', 'vat_rate_on_commission')) {
                $table->decimal('vat_rate_on_commission', 5, 2)->default(19.00)->after('fare_negotiation_step');
            }
            if (!Schema::hasColumn('general_settings', 'driver_cancel_until_status')) {
                $table->unsignedTinyInteger('driver_cancel_until_status')->default(3)->after('vat_rate_on_commission');
            }
        });

        DB::table('general_settings')->update([
            'fare_negotiation_step' => 500,
            'vat_rate_on_commission' => 19.00,
            'driver_cancel_until_status' => 3,
        ]);

        // B2: intercambiar orden visual servicios #3 y #4 (ids 3 y 4)
        $order3 = DB::table('vehicle_services')->where('id', 3)->value('display_order');
        $order4 = DB::table('vehicle_services')->where('id', 4)->value('display_order');
        if ($order3 !== null && $order4 !== null) {
            DB::table('vehicle_services')->where('id', 3)->update(['display_order' => $order4]);
            DB::table('vehicle_services')->where('id', 4)->update(['display_order' => $order3]);
        }
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            foreach (['fare_negotiation_step', 'vat_rate_on_commission', 'driver_cancel_until_status'] as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
