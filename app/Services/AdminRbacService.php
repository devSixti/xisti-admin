<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminModule;
use App\Models\AdminRole;
use App\Models\RoleModulePermission;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves RBAC menu and route access from role_module_permissions.
 */
class AdminRbacService
{
    public const ACTION_VIEW = '1';

    public const ACTION_CREATE = '2';

    public const ACTION_EDIT = '3';

    public const ACTION_DELETE = '4';

    public const ACTION_APPROVE = '5';

    public const ACTION_EXPORT = '6';

    public const ACTION_CONFIGURE = '7';

    /** @var array<string, list<string>> */
    private const ROLE_MODULE_MATRIX = [
        'admin_total' => ['*'],
        'contabilidad' => [
            'dashboard', 'earning-report', 'cash-out-list', 'world-currency-list',
            'vehicle-commission-rates', 'service-settings', 'referral-history',
        ],
        'socio' => [
            'dashboard', 'transport-providers-list', 'customer-list', 'ride-list',
            'earning-report', 'heat-map', 'search-radius', 'geo-fencing-list',
        ],
        'desarrollador' => [
            'dashboard', 'app-version-setting', 'site-setting', 'support-page-list',
            'push-notification', 'api-keys', 'audit-logs', 'security',
        ],
        'aprobaciones' => [
            'dashboard', 'pending-provider-list', 'pending-transport-provider-list',
            'pending-delivery-person-list', 'transport-providers-list', 'driver-documents',
        ],
        'soporte' => [
            'dashboard', 'customer-list', 'transport-providers-list', 'ride-list',
            'sos-list', 'support-page-list', 'push-notification', 'report-issue',
        ],
        'marketing' => [
            'dashboard', 'push-notification', 'support-page-list', 'referral-history',
            'coupon-deals', 'promotions',
        ],
    ];

    /** @var array<string, string> */
    private const MODULE_KEY_ALIASES = [
        'customer-list' => 'customer-list',
        'transport-providers-list' => 'transport-providers-list',
        'transprort-providers-list' => 'transport-providers-list',
        'ride-list' => 'ride-list',
        'earning-report' => 'earning-report',
        'cash-out-list' => 'cash-out-list',
        'world-currency-list' => 'world-currency-list',
        'vehicle-commission-rates' => 'vehicle-commission-rates',
        'service-settings' => 'service-settings',
        'site-setting' => 'site-setting',
        'app-version-setting' => 'app-version-setting',
        'push-notification' => 'push-notification',
        'support-page-list' => 'support-page-list',
        'pending-provider-list' => 'pending-provider-list',
        'pending-transport-provider-list' => 'pending-transport-provider-list',
        'pending-delivery-person-list' => 'pending-delivery-person-list',
        'heat-map' => 'heat-map',
        'search-radius' => 'search-radius',
        'geo-fencing-list' => 'geo-fencing-list',
        'sos-list' => 'sos-list',
        'referral-history' => 'referral-history',
        'dashboard' => 'dashboard',
    ];

    public function resolveRole(Admin $admin): ?AdminRole
    {
        if (! Schema::hasTable('admin_roles')) {
            return null;
        }
        if ($admin->role_id) {
            return AdminRole::query()->find($admin->role_id);
        }

        return AdminRole::query()->where('legacy_role', (int) $admin->roles)->first();
    }

    public function usesRbac(Admin $admin): bool
    {
        return $this->resolveRole($admin) !== null
            && Schema::hasTable('role_module_permissions');
    }

    /** @return list<int> */
    public function permittedModuleIds(Admin $admin): array
    {
        $role = $this->resolveRole($admin);
        if ($role === null) {
            return [];
        }

        return RoleModulePermission::query()
            ->where('role_id', $role->id)
            ->whereRaw("find_in_set(?, permissions)", [self::ACTION_VIEW])
            ->pluck('module_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function canAccessRoute(Admin $admin, ?string $routeName): bool
    {
        if ($routeName === null || $routeName === '') {
            return true;
        }
        if ((int) $admin->roles === 1 && ! $this->usesRbac($admin)) {
            return true;
        }
        $role = $this->resolveRole($admin);
        if ($role === null) {
            return (int) $admin->roles === 1;
        }
        if ($role->slug === 'admin_total') {
            return true;
        }

        $module = AdminModule::query()
            ->where('status', 1)
            ->where(function ($q) use ($routeName) {
                $q->where('route_path', $routeName)
                    ->orWhereRaw('find_in_set(?, route_path_arr)', [$routeName]);
            })
            ->first();

        if ($module === null) {
            return in_array($routeName, ['get:admin:dashboard', 'post:admin:dashboard'], true);
        }

        return RoleModulePermission::query()
            ->where('role_id', $role->id)
            ->where('module_id', $module->id)
            ->whereRaw("find_in_set(?, permissions)", [self::ACTION_VIEW])
            ->exists();
    }

    /** @return array<int, array{parent_menu: array, child_menu: array}> */
    public function buildMenuForAdmin(Admin $admin): array
    {
        $permittedIds = $this->permittedModuleIds($admin);
        if ($permittedIds === [] && (int) $admin->roles === 1) {
            return [];
        }

        $parents = AdminModule::query()
            ->where('status', 1)
            ->where('parent_id', 0)
            ->whereIn('id', $this->parentIdsFromPermitted($permittedIds))
            ->orderBy('seq')
            ->get();

        $menu = [];
        foreach ($parents as $parent) {
            $children = AdminModule::query()
                ->where('status', 1)
                ->where('parent_id', $parent->id)
                ->whereIn('id', $permittedIds)
                ->orderBy('seq')
                ->get()
                ->map(fn ($child) => $this->moduleToMenuEntry($child))
                ->all();

            if ($children !== [] || in_array($parent->id, $permittedIds, true)) {
                $menu[] = [
                    'parent_menu' => $this->moduleToMenuEntry($parent),
                    'child_menu' => $children,
                ];
            }
        }

        return $menu;
    }

    /** @param list<int> $permittedIds */
    private function parentIdsFromPermitted(array $permittedIds): array
    {
        $parents = AdminModule::query()
            ->whereIn('id', $permittedIds)
            ->pluck('parent_id')
            ->merge($permittedIds)
            ->unique()
            ->filter(fn ($id) => (int) $id >= 0)
            ->values()
            ->all();

        return array_map('intval', $parents);
    }

    private function moduleToMenuEntry(AdminModule $module): array
    {
        return [
            'id' => $module->id,
            'parent_id' => $module->parent_id,
            'name' => $module->name,
            'module_name' => $module->module_name,
            'route_path' => $module->route_path,
            'route_path_arr' => $module->route_path_arr,
            'image' => $module->image,
        ];
    }

    /** @return array<string, list<string>> */
    public static function roleModuleMatrix(): array
    {
        return self::ROLE_MODULE_MATRIX;
    }

    public static function normalizeModuleKey(?string $moduleName, ?string $routePath): string
    {
        $key = trim((string) $moduleName);
        if ($key === '' && $routePath !== null) {
            $key = str_replace(['get:admin:', 'post:admin:'], '', $routePath);
        }
        $key = strtolower(str_replace(['/', '_'], '-', $key));

        return self::MODULE_KEY_ALIASES[$key] ?? $key;
    }

    public function seedRolePermissions(): void
    {
        if (! Schema::hasTable('admin_roles') || ! Schema::hasTable('role_module_permissions')) {
            return;
        }

        $modules = AdminModule::query()->where('status', 1)->get();
        $modulesByKey = [];
        foreach ($modules as $module) {
            $key = self::normalizeModuleKey($module->module_name, $module->route_path);
            $modulesByKey[$key] = $module->id;
        }

        foreach (self::ROLE_MODULE_MATRIX as $roleSlug => $moduleKeys) {
            $role = AdminRole::query()->where('slug', $roleSlug)->first();
            if ($role === null) {
                continue;
            }
            RoleModulePermission::query()->where('role_id', $role->id)->delete();

            if ($moduleKeys === ['*']) {
                foreach ($modules as $module) {
                    $this->grant($role->id, (int) $module->id, '1,2,3,4,5,6,7');
                }
                continue;
            }

            foreach ($moduleKeys as $key) {
                if (! isset($modulesByKey[$key])) {
                    continue;
                }
                $perms = match ($roleSlug) {
                    'contabilidad' => '1,6',
                    'aprobaciones' => '1,5',
                    'marketing' => '1,2,3',
                    'desarrollador' => '1,7',
                    default => '1,2,3',
                };
                $this->grant($role->id, (int) $modulesByKey[$key], $perms);
            }
        }
    }

    private function grant(int $roleId, int $moduleId, string $permissions): void
    {
        RoleModulePermission::query()->updateOrCreate(
            ['role_id' => $roleId, 'module_id' => $moduleId],
            ['permissions' => $permissions]
        );
    }
}
