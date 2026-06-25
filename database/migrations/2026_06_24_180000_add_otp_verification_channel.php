<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_verification') && ! Schema::hasColumn('user_verification', 'verification_channel')) {
            Schema::table('user_verification', function (Blueprint $table) {
                $table->string('verification_channel', 16)->nullable()->default('sms')->after('token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_verification') && Schema::hasColumn('user_verification', 'verification_channel')) {
            Schema::table('user_verification', function (Blueprint $table) {
                $table->dropColumn('verification_channel');
            });
        }
    }
};
