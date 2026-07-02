<?php

namespace Tests\Unit;

use App\Helpers\DriverDocumentGateHelper;
use App\Helpers\RideLifecycleHelper;
use App\Helpers\ServiceCatalogHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowGuardHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_service_ids_include_native_transport_service(): void
    {
        DB::table('transport_vehicle_type')->insert([
            'id' => 10,
            'service_id' => 1,
            'name' => 'Sedán',
            'icon_name' => 'sedan.png',
            'cost_for_km' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('vehicle_type_service_eligibility')->insert([
            'vehicle_type_id' => 10,
            'service_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ids = ServiceCatalogHelper::eligibleServiceIdsForVehicleType(10, 1);

        $this->assertContains(1, $ids);
        $this->assertContains(4, $ids);
    }

    public function test_ride_timeout_from_now_is_in_the_future(): void
    {
        DB::table('service_settings')->insert([
            'ride_expiry' => 30,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timeout = RideLifecycleHelper::rideTimeoutFromNow();

        $this->assertGreaterThan(time(), strtotime($timeout));
    }

    public function test_driver_document_gate_blocks_missing_documents(): void
    {
        DB::table('general_settings')->insert([
            'auto_approve' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('required_documents')->insert([
            'id' => 1,
            'name' => 'Licencia',
            'status' => 1,
            'contains_expiry' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        request()->attributes->set('general_settings', (object) ['auto_approve' => 0]);

        $response = DriverDocumentGateHelper::onlineBlockResponse(99);

        $this->assertNotNull($response);
        $this->assertSame(370, $response->getData(true)['message_code']);
    }
}
