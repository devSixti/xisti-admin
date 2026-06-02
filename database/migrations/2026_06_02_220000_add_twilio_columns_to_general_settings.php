<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'twilio_service_key')) {
                $table->string('twilio_service_key', 191)->nullable();
            }
            if (! Schema::hasColumn('general_settings', 'twilio_auth_token')) {
                $table->string('twilio_auth_token', 191)->nullable();
            }
            if (! Schema::hasColumn('general_settings', 'twilio_verify_service_key')) {
                $table->string('twilio_verify_service_key', 191)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            foreach (['twilio_service_key', 'twilio_auth_token', 'twilio_verify_service_key'] as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
