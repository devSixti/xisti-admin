<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            //
            $table->tinyInteger('is_google_login')->default(0)->comment('0 - off google login 1- on google login')->after('auto_settle_wallet');
            $table->tinyInteger('is_facebook_login')->default(0)->comment('0 - off facebook login 1- on facebook login')->after('is_google_login');
            $table->tinyInteger('is_apple_login')->default(0)->comment('0 - off apple login 1- on apple login')->after('is_facebook_login');
            $table->tinyInteger('is_finger_login')->default(0)->comment('0 - off finger login 1- on finger login')->after('is_apple_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            //
            $table->dropColumn('is_google_login');
            $table->dropColumn('is_facebook_login');
            $table->dropColumn('is_apple_login');
            $table->dropColumn('is_finger_login');
        });
    }
};
