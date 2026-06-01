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
            $table->string('doc_expiry_warning_one',20)->after('auto_approve')->nullable();
            $table->string('doc_expiry_warning_two',20)->after('doc_expiry_warning_one')->nullable();
            $table->string('doc_expiry_warning_three',20)->after('doc_expiry_warning_one')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            //
        });
    }
};
