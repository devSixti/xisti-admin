<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admin_mfa_challenges')) {
            return;
        }

        Schema::create('admin_mfa_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('token_hash', 64);
            $table->string('ip', 45)->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['admin_id', 'expires_at']);
            $table->index('token_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_mfa_challenges');
    }
};
