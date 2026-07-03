<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_verification')) {
            return;
        }

        if (! Schema::hasColumn('user_verification', 'verification_sent_at')) {
            Schema::table('user_verification', function (Blueprint $table) {
                $table->timestamp('verification_sent_at')->nullable()->after('verification_channel');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_verification') && Schema::hasColumn('user_verification', 'verification_sent_at')) {
            Schema::table('user_verification', function (Blueprint $table) {
                $table->dropColumn('verification_sent_at');
            });
        }
    }
};
