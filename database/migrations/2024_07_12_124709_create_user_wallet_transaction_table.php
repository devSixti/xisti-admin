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
        // Check if the table does not exist then create
        if (!Schema::hasTable('user_wallet_transaction')) {
            Schema::create('user_wallet_transaction', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->tinyInteger('wallet_provider_type')->default(0);
                $table->tinyInteger('transaction_type')->default(1);
                $table->double('amount');
                $table->string('order_no', 191)->nullable();
                $table->string('subject', 191);
                $table->integer('subject_code');
                $table->double('remaining_balance');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallet_transaction');
    }
};
