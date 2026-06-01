<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_courier_service_details')
            && !Schema::hasColumn('user_courier_service_details', 'requested_vehicle_service_id')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->unsignedInteger('requested_vehicle_service_id')->nullable()->after('ride_id');
            });
        }

        if (Schema::hasTable('transport_driver_details')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                if (! Schema::hasColumn('transport_driver_details', 'accept_transport')) {
                    $table->tinyInteger('accept_transport')->default(0);
                }
                if (! Schema::hasColumn('transport_driver_details', 'accept_delivery')) {
                    $table->tinyInteger('accept_delivery')->default(0);
                }
            });

            if (! Schema::hasColumn('transport_driver_details', 'also_transport_passengers')) {
                Schema::table('transport_driver_details', function (Blueprint $table) {
                    if (Schema::hasColumn('transport_driver_details', 'accept_delivery')) {
                        $table->tinyInteger('also_transport_passengers')->default(0)->after('accept_delivery');
                    } else {
                        $table->tinyInteger('also_transport_passengers')->default(0);
                    }
                });
            }
        }

        if (Schema::hasTable('vehicle_type_service_eligibility')) {
            $transportTypeIds = DB::table('transport_vehicle_type')
                ->whereIn('service_id', [1, 3, 5])
                ->pluck('id');
            foreach ($transportTypeIds as $typeId) {
                $exists = DB::table('vehicle_type_service_eligibility')
                    ->where('vehicle_type_id', $typeId)
                    ->where('service_id', 4)
                    ->exists();
                if (!$exists) {
                    DB::table('vehicle_type_service_eligibility')->insert([
                        'vehicle_type_id' => $typeId,
                        'service_id' => 4,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_courier_service_details')
            && Schema::hasColumn('user_courier_service_details', 'requested_vehicle_service_id')) {
            Schema::table('user_courier_service_details', function (Blueprint $table) {
                $table->dropColumn('requested_vehicle_service_id');
            });
        }
        if (Schema::hasTable('transport_driver_details')
            && Schema::hasColumn('transport_driver_details', 'also_transport_passengers')) {
            Schema::table('transport_driver_details', function (Blueprint $table) {
                $table->dropColumn('also_transport_passengers');
            });
        }
    }
};
