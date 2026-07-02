<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $legacyRoutePaths = [
        'get:admin:delivery_person_list',
        'get:admin:provider_list',
        'get:admin:pending_delivery_person_list',
        'get:admin:pending_transport_provider_list',
        'get:admin:pending_store_provider_list',
        'get:admin:pending_other_provider_list',
        'get:admin:home_page_slider_list',
        'get:admin:home_page_feature_store_list',
        'get:admin:ordering_service_category_list',
        'get:admin:home_page_spot_light_list',
        'get:admin:home_page_top_service_list',
    ];

    public function up(): void
    {
        DB::table('admin_module')
            ->whereIn('route_path', $this->legacyRoutePaths)
            ->update(['status' => 0]);
    }

    public function down(): void
    {
        // Legacy modules have no matching routes; keep disabled on rollback.
    }
};
