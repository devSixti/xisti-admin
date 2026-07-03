<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_services') && Schema::hasColumn('vehicle_services', 'service_mode')) {
            $expresoUpdate = [
                'service_mode' => 'viajes_compartidos',
                'name' => 'Viajes compartidos',
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('vehicle_services', 'es_name')) {
                $expresoUpdate['es_name'] = 'Viajes compartidos';
            }
            if (Schema::hasColumn('vehicle_services', 'en_name')) {
                $expresoUpdate['en_name'] = 'Shared rides';
            }
            if (Schema::hasColumn('vehicle_services', 'vehicle_service_description')) {
                $expresoUpdate['vehicle_service_description'] = 'Viajes compartidos pueblo a pueblo o pueblo a ciudad';
            }
            DB::table('vehicle_services')
                ->where('service_mode', 'expreso')
                ->update($expresoUpdate);

            if (! DB::table('vehicle_services')->where('service_mode', 'acarreos')->exists()) {
                $now = now();
                $row = [
                    'name' => 'Acarreos',
                    'service_mode' => 'acarreos',
                    'status' => 1,
                    'cost_for_km' => 0,
                    'vehicle_service_description' => 'Acarreos y mudanzas ligeras',
                    'icon_name' => '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (Schema::hasColumn('vehicle_services', 'es_name')) {
                    $row['es_name'] = 'Acarreos';
                }
                if (Schema::hasColumn('vehicle_services', 'en_name')) {
                    $row['en_name'] = 'Hauling';
                }
                if (Schema::hasColumn('vehicle_services', 'display_order')) {
                    $row['display_order'] = 35;
                }
                if (Schema::hasColumn('vehicle_services', 'max_bargain_percent')) {
                    $row['max_bargain_percent'] = 0;
                    $row['max_offer_percent'] = 0;
                }
                DB::table('vehicle_services')->insert($row);
            }

            $acarreoServiceId = (int) DB::table('vehicle_services')->where('service_mode', 'acarreos')->value('id');
            if ($acarreoServiceId > 0 && Schema::hasTable('transport_vehicle_type')) {
                $types = [
                    ['name' => 'Motocarguero', 'icon_name' => 'motocarguero.png'],
                    ['name' => 'Camión acarreo', 'icon_name' => 'camion_acarreo.png'],
                    ['name' => 'Jaula', 'icon_name' => 'jaula_acarreo.png'],
                ];
                foreach ($types as $type) {
                    $exists = DB::table('transport_vehicle_type')
                        ->where('service_id', $acarreoServiceId)
                        ->where('name', $type['name'])
                        ->exists();
                    if (! $exists) {
                        $row = [
                            'service_id' => $acarreoServiceId,
                            'name' => $type['name'],
                            'icon_name' => $type['icon_name'],
                            'cost_for_km' => 0,
                            'weight_limit' => 0,
                            'width_limit' => 0,
                            'height_limit' => 0,
                            'dimension_limit' => 0,
                            'base_fare' => 0,
                            'time_fare' => 0,
                            'min_fare_amount' => 0,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (Schema::hasColumn('transport_vehicle_type', 'requires_plate')) {
                            $row['requires_plate'] = 1;
                        }
                        if (Schema::hasColumn('transport_vehicle_type', 'requires_vehicle_photos')) {
                            $row['requires_vehicle_photos'] = 1;
                        }
                        DB::table('transport_vehicle_type')->insert($row);
                    }
                }
            }
        }

        if (! Schema::hasTable('shared_ride_offers')) {
            Schema::create('shared_ride_offers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('driver_id');
                $table->unsignedBigInteger('driver_vehicle_type_id')->nullable();
                $table->string('trip_kind', 32);
                $table->string('origin_town', 120);
                $table->string('destination_town', 120);
                $table->date('trip_date');
                $table->unsignedSmallInteger('seats_total');
                $table->unsignedSmallInteger('seats_available');
                $table->string('status', 20)->default('open');
                $table->unsignedBigInteger('ride_id')->nullable();
                $table->timestamps();
                $table->index(['trip_kind', 'origin_town', 'destination_town', 'trip_date', 'status'], 'shared_ride_match_idx');
            });
        }

        if (! Schema::hasTable('shared_ride_passenger_searches')) {
            Schema::create('shared_ride_passenger_searches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('trip_kind', 32);
                $table->string('origin_town', 120);
                $table->string('destination_town', 120);
                $table->date('trip_date');
                $table->string('status', 20)->default('searching');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('shared_ride_members')) {
            Schema::create('shared_ride_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('offer_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('search_id')->nullable();
                $table->unsignedBigInteger('ride_id')->nullable();
                $table->string('status', 20)->default('joined');
                $table->timestamps();
                $table->unique(['offer_id', 'user_id']);
            });
        }

        if (Schema::hasTable('general_settings') && ! Schema::hasColumn('general_settings', 'enable_acarreos_mobile')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->unsignedTinyInteger('enable_acarreos_mobile')->default(1);
            });
        }

        if (Schema::hasTable('user_courier_service_details')) {
            if (! Schema::hasColumn('user_courier_service_details', 'acarreo_vehicle_variant')) {
                Schema::table('user_courier_service_details', function (Blueprint $table) {
                    $table->string('acarreo_vehicle_variant', 32)->nullable();
                });
            }
            if (! Schema::hasColumn('user_courier_service_details', 'estimated_service_date')) {
                Schema::table('user_courier_service_details', function (Blueprint $table) {
                    $table->date('estimated_service_date')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_ride_members');
        Schema::dropIfExists('shared_ride_passenger_searches');
        Schema::dropIfExists('shared_ride_offers');

        if (Schema::hasTable('vehicle_services')) {
            DB::table('vehicle_services')->where('service_mode', 'acarreos')->delete();
            DB::table('vehicle_services')
                ->where('service_mode', 'viajes_compartidos')
                ->update(['service_mode' => 'expreso', 'name' => 'Expreso']);
        }

        if (Schema::hasColumn('general_settings', 'enable_acarreos_mobile')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->dropColumn('enable_acarreos_mobile');
            });
        }
    }
};
