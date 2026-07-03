<?php

namespace App\Console\Commands;

use App\Models\AdminRole;
use App\Services\AdminRbacService;
use Illuminate\Console\Command;

class VerifyRbacMatrixCommand extends Command
{
    protected $signature = 'rbac:verify {--role= : Verify a single role slug}';

    protected $description = 'Verify RBAC matrix mapping, seeded permissions, and route access per role';

    public function handle(AdminRbacService $rbac): int
    {
        $unresolved = $rbac->unresolvedMatrixKeys();
        if ($unresolved !== []) {
            $this->error('Unresolved matrix keys: '.implode(', ', $unresolved));

            return self::FAILURE;
        }
        $this->info('All matrix keys resolve to active modules (or are virtual).');

        $roles = AdminRole::query()
            ->when($this->option('role'), fn ($q) => $q->where('slug', $this->option('role')))
            ->orderBy('id')
            ->get();

        $failed = 0;
        foreach ($roles as $role) {
            $this->line('');
            $this->info("Role: {$role->slug}");
            $keys = $rbac->allowedMatrixKeysForRole($role);
            $this->line('  Allowed keys: '.implode(', ', $keys));

            foreach ($keys as $key) {
                if ($key === 'dashboard' || AdminRbacService::isVirtualMatrixKey($key)) {
                    continue;
                }
                if (in_array($key, AdminRbacService::DEFERRED_MATRIX_KEYS, true)) {
                    continue;
                }
                $moduleIds = $rbac->moduleIdsForMatrixKey($key);
                if ($moduleIds === []) {
                    $this->error("  MISSING mapping for key [{$key}]");
                    $failed++;
                }
            }

            $denied = $rbac->deniedMatrixKeysForRole($role);
            foreach ($denied as $key) {
                foreach ($rbac->sampleRoutesForMatrixKey($key) as $route) {
                    $admin = $this->syntheticAdmin($role);
                    if ($rbac->canAccessRoute($admin, $route)) {
                        $this->error("  FAIL denied route accessible: {$route} ({$key})");
                        $failed++;
                    }
                }
            }

            foreach ($keys as $key) {
                if ($key === 'dashboard') {
                    continue;
                }
                foreach ($rbac->sampleRoutesForMatrixKey($key) as $route) {
                    $admin = $this->syntheticAdmin($role);
                    if (! $rbac->canAccessRoute($admin, $route)) {
                        $this->error("  FAIL allowed route blocked: {$route} ({$key})");
                        $failed++;
                    }
                }
            }
        }

        if ($failed > 0) {
            $this->error("RBAC verification failed ({$failed} issue(s)).");

            return self::FAILURE;
        }

        $this->info('RBAC matrix verification passed.');

        return self::SUCCESS;
    }

    private function syntheticAdmin(AdminRole $role): \App\Models\Admin
    {
        $admin = new \App\Models\Admin;
        $admin->roles = $role->legacy_role ?? 4;
        $admin->role_id = $role->id;

        return $admin;
    }
}
