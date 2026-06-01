<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'wompi_mode')) {
                $table->tinyInteger('wompi_mode')->default(0)->comment('0:sandbox,1:production')->after('card_payment');
            }

            if (!Schema::hasColumn('general_settings', 'wompi_sandbox_public_key')) {
                $table->string('wompi_sandbox_public_key', 191)->nullable()->after('wompi_mode');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_sandbox_private_key')) {
                $table->string('wompi_sandbox_private_key', 191)->nullable()->after('wompi_sandbox_public_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_sandbox_event_key')) {
                $table->string('wompi_sandbox_event_key', 191)->nullable()->after('wompi_sandbox_private_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_sandbox_integrity_key')) {
                $table->string('wompi_sandbox_integrity_key', 191)->nullable()->after('wompi_sandbox_event_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_sandbox_base_url')) {
                $table->string('wompi_sandbox_base_url', 191)->nullable()->after('wompi_sandbox_integrity_key');
            }

            if (!Schema::hasColumn('general_settings', 'wompi_production_public_key')) {
                $table->string('wompi_production_public_key', 191)->nullable()->after('wompi_sandbox_base_url');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_production_private_key')) {
                $table->string('wompi_production_private_key', 191)->nullable()->after('wompi_production_public_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_production_event_key')) {
                $table->string('wompi_production_event_key', 191)->nullable()->after('wompi_production_private_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_production_integrity_key')) {
                $table->string('wompi_production_integrity_key', 191)->nullable()->after('wompi_production_event_key');
            }
            if (!Schema::hasColumn('general_settings', 'wompi_production_base_url')) {
                $table->string('wompi_production_base_url', 191)->nullable()->after('wompi_production_integrity_key');
            }
        });

        DB::table('general_settings')->update([
            'wompi_mode' => DB::raw('COALESCE(wompi_mode, 0)'),
            'wompi_sandbox_public_key' => DB::raw("COALESCE(wompi_sandbox_public_key, 'pub_test_J9n1XaJk3UbihupazVwhNQnnFlOj4grW')"),
            'wompi_sandbox_private_key' => DB::raw("COALESCE(wompi_sandbox_private_key, 'prv_test_HCVzn7d2TPrxY8Yrlv2bVhqppK6YfYGU')"),
            'wompi_sandbox_event_key' => DB::raw("COALESCE(wompi_sandbox_event_key, 'prod_events_f8op2oIvLTi46aOC0HHc5kKvdjq2B00i')"),
            'wompi_sandbox_integrity_key' => DB::raw("COALESCE(wompi_sandbox_integrity_key, 'prod_integrity_2m6Vi0h0tjOdbSG6IMITMuwaCaLMSQxS')"),
            'wompi_sandbox_base_url' => DB::raw("COALESCE(wompi_sandbox_base_url, 'https://sandbox.wompi.co/v1')"),
            'wompi_production_base_url' => DB::raw("COALESCE(wompi_production_base_url, 'https://production.wompi.co/v1')"),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'wompi_mode',
                'wompi_sandbox_public_key',
                'wompi_sandbox_private_key',
                'wompi_sandbox_event_key',
                'wompi_sandbox_integrity_key',
                'wompi_sandbox_base_url',
                'wompi_production_public_key',
                'wompi_production_private_key',
                'wompi_production_event_key',
                'wompi_production_integrity_key',
                'wompi_production_base_url',
            ]);
        });
    }
};
