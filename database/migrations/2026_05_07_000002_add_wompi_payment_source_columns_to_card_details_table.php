<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('card_details', function (Blueprint $table) {
            if (!Schema::hasColumn('card_details', 'wompi_payment_source_id')) {
                $table->unsignedBigInteger('wompi_payment_source_id')->nullable()->after('cvv');
            }
            if (!Schema::hasColumn('card_details', 'wompi_card_brand')) {
                $table->string('wompi_card_brand', 40)->nullable()->after('wompi_payment_source_id');
            }
            if (!Schema::hasColumn('card_details', 'wompi_card_last_four')) {
                $table->string('wompi_card_last_four', 4)->nullable()->after('wompi_card_brand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_details', function (Blueprint $table) {
            $table->dropColumn([
                'wompi_payment_source_id',
                'wompi_card_brand',
                'wompi_card_last_four',
            ]);
        });
    }
};
