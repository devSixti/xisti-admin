<?php

use App\Helpers\DestinationPaymentHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_settings') || ! Schema::hasColumn('general_settings', 'destination_payment_methods')) {
            return;
        }

        $row = DB::table('general_settings')->first();
        if ($row === null) {
            return;
        }

        $current = trim((string) ($row->destination_payment_methods ?? ''));
        if ($current !== '') {
            return;
        }

        DB::table('general_settings')->update([
            'destination_payment_methods' => json_encode(
                DestinationPaymentHelper::defaultCatalog(),
                JSON_UNESCAPED_UNICODE
            ),
        ]);
    }

    public function down(): void
    {
        // Non-destructive: keep admin-edited catalog.
    }
};
