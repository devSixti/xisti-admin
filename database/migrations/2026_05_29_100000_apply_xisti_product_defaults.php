<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $commission = (float) config('xisti.default_commission_percent', 8);
        $negotiationStep = (int) config('xisti.fare_negotiation_step_cop', 500);

        if (Schema::hasTable('service_settings')) {
            $payload = ['admin_commission' => $commission];
            if (Schema::hasColumn('service_settings', 'admin_hail_commission')) {
                $payload['admin_hail_commission'] = $commission;
            }
            DB::table('service_settings')->update($payload);
        }

        if (Schema::hasTable('general_settings')) {
            $general = [
                'website_name' => config('xisti.product_name', 'XISTI'),
                'site_url' => 'https://admin.xistiapp.com/',
                'copy_right' => '© Copyright '.date('Y').' '.config('xisti.product_name', 'XISTI').' App',
                'fcm_user_topic_name' => config('xisti.fcm_user_topic', 'XistiUser'),
                'fcm_driver_topic_name' => config('xisti.fcm_driver_topic', 'XistiDriver'),
            ];

            if (Schema::hasColumn('general_settings', 'fare_negotiation_step')) {
                $general['fare_negotiation_step'] = $negotiationStep;
            }
            if (Schema::hasColumn('general_settings', 'vat_rate_on_commission')) {
                $general['vat_rate_on_commission'] = 19.00;
            }
            if (Schema::hasColumn('general_settings', 'driver_cancel_until_status')) {
                $general['driver_cancel_until_status'] = 3;
            }
            if (Schema::hasColumn('general_settings', 'enable_expreso_mobile')) {
                $general['enable_expreso_mobile'] = 0;
            }
            if (Schema::hasColumn('general_settings', 'enable_encomiendas_mobile')) {
                $general['enable_encomiendas_mobile'] = 1;
            }

            DB::table('general_settings')->update($general);
        }
    }

    public function down(): void
    {
        // Product defaults are not reverted automatically.
    }
};
