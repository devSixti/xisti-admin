<?php

use App\Helpers\PushEventTemplateHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_event_templates')) {
            DB::table('push_event_templates')
                ->where('event_key', 'driver_fare_changed_by_user')
                ->update(['app_notification_type' => 1]);
        }

        if (class_exists(PushEventTemplateHelper::class)) {
            PushEventTemplateHelper::syncCatalog();
        }
    }

    public function down(): void
    {
        // Data fix only.
    }
};
