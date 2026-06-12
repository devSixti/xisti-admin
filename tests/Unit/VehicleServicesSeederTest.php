<?php

namespace Tests\Unit;

use Database\Seeders\VehicleServicesSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VehicleServicesSeederTest extends TestCase
{
    public function test_seeder_upserts_all_vehicle_services_without_column_mismatch(): void
    {
        $this->seed(VehicleServicesSeeder::class);

        $rows = DB::table('vehicle_services')->orderBy('id')->get();
        $this->assertGreaterThanOrEqual(4, $rows->count());

        foreach ($rows as $row) {
            $this->assertNotNull($row->min_fare, "vehicle_services.id={$row->id} missing min_fare");
            $this->assertNotNull($row->time_fare, "vehicle_services.id={$row->id} missing time_fare");
            $this->assertNotNull($row->max_offer_percent, "vehicle_services.id={$row->id} missing max_offer_percent");
        }
    }
}
