<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_roles')) {
            Schema::create('admin_roles', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 64)->unique();
                $table->string('name', 128);
                $table->unsignedTinyInteger('legacy_role')->nullable()->comment('Maps to super_admin.roles for backward compatibility');
                $table->boolean('is_system')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_module_permissions')) {
            Schema::create('role_module_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('admin_roles')->cascadeOnDelete();
                $table->unsignedBigInteger('module_id');
                $table->string('permissions', 64)->default('1')->comment('Comma-separated admin_pageaction ids');
                $table->timestamps();
                $table->unique(['role_id', 'module_id']);
            });
        }

        if (Schema::hasTable('super_admin') && ! Schema::hasColumn('super_admin', 'role_id')) {
            Schema::table('super_admin', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('roles');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('super_admin') && Schema::hasColumn('super_admin', 'role_id')) {
            Schema::table('super_admin', function (Blueprint $table) {
                $table->dropColumn('role_id');
            });
        }
        Schema::dropIfExists('role_module_permissions');
        Schema::dropIfExists('admin_roles');
    }
};
