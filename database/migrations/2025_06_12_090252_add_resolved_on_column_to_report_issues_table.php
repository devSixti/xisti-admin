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
        Schema::table('report_issues', function (Blueprint $table) {
            //
            $table->timestamp('resolved_on')->nullable()->comment('When the issue is resolved by admin')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_issues', function (Blueprint $table) {
            //
            $table->dropColumn('resolved_on');
        });
    }
};
