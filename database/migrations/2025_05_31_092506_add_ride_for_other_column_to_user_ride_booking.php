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
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->tinyInteger('ride_for_other')->default(0)->comment('0=not 1=for other')->after('user_refer_history_id');
            $table->string('other_user_name', 191)->nullable()->after('ride_for_other');
            $table->string('other_user_contact_number', 15)->nullable()->after('other_user_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ride_booking', function (Blueprint $table) {
            //
            $table->dropColumn('ride_for_other');
            $table->dropColumn('other_user_name');
            $table->dropColumn('other_user_contact_number');
        });
    }
};
