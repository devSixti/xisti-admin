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
        // Check if the table does not exist then create
        if (!Schema::hasTable('report_issues')) {
            Schema::create('report_issues', function (Blueprint $table) {
                $table->id();
                $table->string('reference_no', 191)->default(0);
                $table->string('description', 250);
                $table->integer('ride_id')->length(11)->default(0);
                $table->unsignedInteger('provider_id')->length(11);
                $table->tinyInteger('provider_type')->length(4)->default(0)->comment('1:user,2:driver');
                $table->tinyInteger('status')->comment('0=draft, 1=unresolved, 2=resolved');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_issues');
    }
};
