<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'accepted_terms_at')) {
                $table->timestamp('accepted_terms_at')->nullable()->after('is_register');
            }
            if (! Schema::hasColumn('users', 'accepted_data_processing_at')) {
                $table->timestamp('accepted_data_processing_at')->nullable()->after('accepted_terms_at');
            }
            if (! Schema::hasColumn('users', 'accepted_legal_version')) {
                $table->string('accepted_legal_version', 64)->nullable()->after('accepted_data_processing_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = ['accepted_terms_at', 'accepted_data_processing_at', 'accepted_legal_version'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
