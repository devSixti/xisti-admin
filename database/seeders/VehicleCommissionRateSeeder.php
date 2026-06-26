<?php

namespace Database\Seeders;

use App\Helpers\VehicleCommissionHelper;
use App\Models\VehicleCommissionRate;
use Illuminate\Database\Seeder;

class VehicleCommissionRateSeeder extends Seeder
{
    public function run(): void
    {
        $default = VehicleCommissionHelper::globalDefaultPercent();

        foreach (VehicleCommissionHelper::defaultCatalog() as $item) {
            VehicleCommissionRate::query()->updateOrCreate(
                ['variant_key' => $item['variant_key']],
                [
                    'label' => $item['label'],
                    'vehicle_service_id' => $item['vehicle_service_id'],
                    'commission_percent' => $default,
                    'sort_order' => $item['sort_order'],
                    'status' => 1,
                ]
            );
        }
    }
}
