<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        if (! Schema::hasColumn('general_settings', 'destination_payment_methods')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (Schema::hasColumn('general_settings', 'vat_rate_on_commission')) {
                    $table->text('destination_payment_methods')->nullable()->after('vat_rate_on_commission');
                } else {
                    $table->text('destination_payment_methods')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'destination_payment_methods')) {
                $table->dropColumn('destination_payment_methods');
            }
        });
    }
};
