<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\AdminModule;
use App\Models\AdminRole;
use App\Models\RoleModulePermission;
use App\Services\AdminRbacService;
use Database\Seeders\AdminModuleSeeder;
use Database\Seeders\AdminRbacMatrixSeeder;
use Database\Seeders\AdminRbacMockUsersSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminRbacMatrixTest extends TestCase
{
    private AdminRbacService $rbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminModuleSeeder::class);
        $this->seed(AdminRbacMatrixSeeder::class);
        $this->rbac = app(AdminRbacService::class);
    }

  public static function nonAdminTotalRoleProvider(): array
    {
        return array_map(
            static fn (string $slug): array => [$slug],
            array_values(array_filter(
                AdminRbacService::allRoleSlugs(),
                static fn (string $slug): bool => $slug !== 'admin_total'
            ))
        );
    }

    #[Test]
    public function all_non_virtual_matrix_keys_resolve_to_active_modules(): void
    {
        $unresolved = $this->rbac->unresolvedMatrixKeys();

        $this->assertSame(
            [],
            $unresolved,
            'Unresolved matrix keys without active modules: '.implode(', ', $unresolved)
        );
    }

    #[Test]
    #[DataProvider('nonAdminTotalRoleProvider')]
    public function role_receives_every_resolvable_matrix_module(string $roleSlug): void
    {
        $role = AdminRole::query()->where('slug', $roleSlug)->firstOrFail();
        $expectedKeys = AdminRbacService::roleModuleMatrix()[$roleSlug];
        $expectedActions = AdminRbacService::permissionActionsForRole($roleSlug);

        foreach ($expectedKeys as $key) {
            if ($key === 'dashboard' || AdminRbacService::isVirtualMatrixKey($key)) {
                continue;
            }
            if (in_array($key, AdminRbacService::DEFERRED_MATRIX_KEYS, true)) {
                continue;
            }

            $moduleIds = $this->rbac->moduleIdsForMatrixKey($key);
            if ($moduleIds === []) {
                $this->fail("Role [{$roleSlug}] matrix key [{$key}] has no active module mapping");
            }

            foreach ($moduleIds as $moduleId) {
                $permissions = RoleModulePermission::query()
                    ->where('role_id', $role->id)
                    ->where('module_id', $moduleId)
                    ->value('permissions');

                $this->assertNotNull(
                    $permissions,
                    "Role [{$roleSlug}] missing permission row for module_id={$moduleId} key [{$key}]"
                );

                $granted = explode(',', (string) $permissions);
                foreach ($expectedActions as $action) {
                    $this->assertContains(
                        $action,
                        $granted,
                        "Role [{$roleSlug}] module_id={$moduleId} key [{$key}] missing action [{$action}]"
                    );
                }
            }
        }
    }

    #[Test]
    #[DataProvider('nonAdminTotalRoleProvider')]
    public function role_menu_only_includes_matrix_modules(string $roleSlug): void
    {
        $role = AdminRole::query()->where('slug', $roleSlug)->firstOrFail();
        $admin = $this->adminForRole($role);
        $allowedKeys = AdminRbacService::roleModuleMatrix()[$roleSlug];
        $menu = $this->rbac->buildMenuForAdmin($admin);

        $menuModuleIds = [];
        foreach ($menu as $section) {
            $menuModuleIds[] = (int) $section['parent_menu']['id'];
            foreach ($section['child_menu'] as $child) {
                $menuModuleIds[] = (int) $child['id'];
            }
        }

        foreach (array_unique($menuModuleIds) as $moduleId) {
            $module = AdminModule::query()->find($moduleId);
            if ($module === null) {
                continue;
            }
            $key = $this->rbac->matrixKeyForModule($module);
            if ($key === null || $key === 'dashboard' || $key === '') {
                continue;
            }
            if ($module->route_path === '' && (int) $module->parent_id === 0) {
                continue;
            }
            $this->assertContains(
                $key,
                $allowedKeys,
                "Role [{$roleSlug}] menu includes module outside matrix: {$module->name} ({$key})"
            );
        }
    }

    #[Test]
    #[DataProvider('nonAdminTotalRoleProvider')]
    public function role_cannot_access_modules_outside_matrix(string $roleSlug): void
    {
        $role = AdminRole::query()->where('slug', $roleSlug)->firstOrFail();
        $admin = $this->adminForRole($role);
        $deniedKeys = $this->rbac->deniedMatrixKeysForRole($role);

        foreach ($deniedKeys as $key) {
            foreach ($this->rbac->sampleRoutesForMatrixKey($key) as $routeName) {
                $this->assertFalse(
                    $this->rbac->canAccessRoute($admin, $routeName),
                    "Role [{$roleSlug}] should not access denied route {$routeName} ({$key})"
                );
            }
        }
    }

    #[Test]
    #[DataProvider('nonAdminTotalRoleProvider')]
    public function role_can_access_allowed_sample_routes(string $roleSlug): void
    {
        $role = AdminRole::query()->where('slug', $roleSlug)->firstOrFail();
        $admin = $this->adminForRole($role);

        $this->assertTrue($this->rbac->canAccessRoute($admin, 'get:admin:dashboard'));

        foreach ($this->rbac->allowedMatrixKeysForRole($role) as $key) {
            if ($key === 'dashboard') {
                continue;
            }
            foreach ($this->rbac->sampleRoutesForMatrixKey($key) as $routeName) {
                $this->assertTrue(
                    $this->rbac->canAccessRoute($admin, $routeName),
                    "Role [{$roleSlug}] should access {$routeName} ({$key})"
                );
            }
        }
    }

    #[Test]
    public function desarrollador_can_access_virtual_security_and_audit_routes(): void
    {
        $role = AdminRole::query()->where('slug', 'desarrollador')->firstOrFail();
        $admin = $this->adminForRole($role);

        $this->assertTrue($this->rbac->canAccessRoute($admin, 'get:admin:security'));
        $this->assertTrue($this->rbac->canAccessRoute($admin, 'get:admin:audit_logs'));
    }

    #[Test]
    public function admin_total_has_all_active_module_permissions(): void
    {
        $role = AdminRole::query()->where('slug', 'admin_total')->firstOrFail();
        $activeCount = AdminModule::query()->where('status', 1)->count();
        $grantedCount = RoleModulePermission::query()->where('role_id', $role->id)->count();

        $this->assertSame($activeCount, $grantedCount);
    }

    #[Test]
    #[DataProvider('mockUserEmailProvider')]
    public function mock_rbac_users_use_rbac_and_have_matrix_permissions(string $email, string $roleSlug): void
    {
        $this->seed(AdminRbacMockUsersSeeder::class);
        $admin = Admin::query()->where('email', $email)->firstOrFail();
        $role = AdminRole::query()->where('slug', $roleSlug)->firstOrFail();

        $this->assertTrue($this->rbac->usesRbac($admin));
        $this->assertSame($role->id, $admin->role_id);
        $this->assertNotEmpty($this->rbac->buildMenuForAdmin($admin));

        $expectedResolvable = 0;
        foreach (AdminRbacService::roleModuleMatrix()[$roleSlug] as $key) {
            if ($key === 'dashboard' || AdminRbacService::isVirtualMatrixKey($key)) {
                continue;
            }
            if ($this->rbac->moduleIdsForMatrixKey($key) !== []) {
                $expectedResolvable++;
            }
        }

        $grantedCount = RoleModulePermission::query()->where('role_id', $role->id)->count();
        $this->assertGreaterThanOrEqual(
            max(1, $expectedResolvable),
            $grantedCount,
            "Mock user {$email} should have permissions for all resolvable matrix modules"
        );
    }

    public static function mockUserEmailProvider(): array
    {
        return [
            ['socio@xistiapp.com', 'socio'],
            ['contabilidad@xistiapp.com', 'contabilidad'],
            ['desarrollador@xistiapp.com', 'desarrollador'],
            ['aprobaciones@xistiapp.com', 'aprobaciones'],
            ['soporte@xistiapp.com', 'soporte'],
            ['marketing@xistiapp.com', 'marketing'],
        ];
    }

    private function adminForRole(AdminRole $role): Admin
    {
        $admin = new Admin;
        $admin->roles = $role->legacy_role ?? 4;
        $admin->role_id = $role->id;

        return $admin;
    }
}
