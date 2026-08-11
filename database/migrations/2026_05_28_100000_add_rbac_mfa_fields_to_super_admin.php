<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('super_admin', function (Blueprint $table) {
            if (!Schema::hasColumn('super_admin', 'totp_secret')) {
                $table->text('totp_secret')->nullable()->after('password');
            }
            if (!Schema::hasColumn('super_admin', 'totp_enabled_at')) {
                $table->timestamp('totp_enabled_at')->nullable()->after('totp_secret');
            }
            if (!Schema::hasColumn('super_admin', 'totp_backup_codes')) {
                $table->text('totp_backup_codes')->nullable()->after('totp_enabled_at');
            }
            if (!Schema::hasColumn('super_admin', 'must_change_password')) {
                $table->tinyInteger('must_change_password')->default(0)->after('totp_backup_codes');
            }
            if (!Schema::hasColumn('super_admin', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('must_change_password');
            }
            if (!Schema::hasColumn('super_admin', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('super_admin', 'status')) {
                $table->tinyInteger('status')->default(1)->comment('1=active,0=suspended')->after('last_login_ip');
            }
            if (!Schema::hasColumn('super_admin', 'created_by_admin_id')) {
                $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('super_admin', function (Blueprint $table) {
            foreach ([
                'totp_secret',
                'totp_enabled_at',
                'totp_backup_codes',
                'must_change_password',
                'last_login_at',
                'last_login_ip',
                'status',
                'created_by_admin_id',
            ] as $column) {
                if (Schema::hasColumn('super_admin', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
