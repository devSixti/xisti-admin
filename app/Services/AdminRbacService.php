<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminModule;
use App\Models\AdminRole;
use App\Models\RoleModulePermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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

    /** Documented in RBAC-MATRIX.csv; routes not registered in Laravel yet. */
    public const DEFERRED_MATRIX_KEYS = ['coupon-deals', 'promotions'];

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

    /** Permission CSV strings seeded per role slug (matches RBAC-MATRIX.csv). */
    private const ROLE_PERMISSION_CSV = [
        'contabilidad' => '1,6',
        'aprobaciones' => '1,5',
        'marketing' => '1,2,3',
        'desarrollador' => '1,7',
        'socio' => '1,2,3',
        'soporte' => '1,2,3',
    ];

    /**
     * @var array<string, array{match_urls?: list<string>, module_names?: list<string>, route_paths?: list<string>, exclude_match_urls?: list<string>, exclude_module_names?: list<string>, name_patterns?: list<string>, virtual?: bool}>
     */
    private const MATRIX_MODULE_RULES = [
        'dashboard' => ['virtual' => true],
        'customer-list' => [
            'match_urls' => ['admin/customer-list'],
            'module_names' => ['customer-list'],
        ],
        'transport-providers-list' => [
            'match_urls' => ['transport/provider-list', 'admin/drivers-list/approved', 'admin/add-provider'],
            'module_names' => ['transprort-providers-list', 'transport-providers-list', 'drivers-list', 'add-driver'],
            'exclude_match_urls' => [
                'admin/drivers-list/un-approved',
                'admin/drivers-list/blocked',
                'admin/drivers-list/rejected',
            ],
        ],
        'ride-list' => [
            'match_urls' => ['admin/ride-list'],
            'module_names' => ['rides'],
        ],
        'earning-report' => [
            'match_urls' => ['admin/earning-report'],
            'module_names' => ['earning report'],
        ],
        'heat-map' => [
            'match_urls' => ['admin/transport-heat-map'],
            'module_names' => ['heat map'],
        ],
        'search-radius' => [
            'match_urls' => ['admin/search-radius'],
            'module_names' => ['search radius'],
        ],
        'geo-fencing-list' => [
            'match_urls' => ['restricted-area-list'],
            'module_names' => ['restricted-area-list', 'geo-fencing-list'],
        ],
        'cash-out-list' => [
            'match_urls' => ['/cash-out', 'admin/cash-out'],
            'module_names' => ['/cash-out', 'cash-out-list'],
        ],
        'world-currency-list' => [
            'match_urls' => ['admin/world-currency-list'],
            'module_names' => ['world-currency-list'],
        ],
        'vehicle-commission-rates' => [
            'match_urls' => ['admin/vehicle-commission-rates'],
            'module_names' => ['vehicle-commission-rates'],
        ],
        'service-settings' => [
            'match_urls' => ['admin/service-setting'],
        ],
        'site-setting' => [
            'match_urls' => ['admin/site-setting'],
            'module_names' => ['site-setting'],
        ],
        'app-version-setting' => [
            'match_urls' => ['admin/app-version-setting'],
            'module_names' => ['app-version-setting'],
        ],
        'push-notification' => [
            'match_urls' => ['admin/push-notification'],
            'module_names' => ['push-notification'],
        ],
        'support-page-list' => [
            'match_urls' => ['admin/support-page-list'],
            'module_names' => ['support-page-list'],
        ],
        'pending-provider-list' => [
            'match_urls' => ['admin/pending-provider-list', 'admin/drivers-list/un-approved'],
            'module_names' => ['pending-provider-list'],
        ],
        'pending-transport-provider-list' => [
            'match_urls' => ['admin/drivers-list/un-approved', 'transport/provider-list'],
            'module_names' => ['pending-transport-provider-list'],
            'route_paths' => ['get:admin:pending_transport_provider_list'],
        ],
        'pending-delivery-person-list' => [
            'match_urls' => ['admin/delivery-person-list', 'admin/drivers-list/un-approved'],
            'module_names' => ['delivery-person-list', 'pending-delivery-person-list'],
            'route_paths' => ['get:admin:pending_delivery_person_list'],
        ],
        'driver-documents' => [
            'match_urls' => ['admin/required-document-list'],
            'module_names' => ['driver-documents', 'required-document-list'],
        ],
        'sos-list' => [
            'match_urls' => ['sos/manage'],
            'module_names' => ['manage', 'sos-list'],
        ],
        'referral-history' => [
            'match_urls' => ['admin/referral-list'],
            'module_names' => ['referral'],
        ],
        'coupon-deals' => [
            'match_urls' => ['coupon-deals/service-list'],
            'module_names' => ['coupon-deals/service-list', 'coupon-deals'],
        ],
        'promotions' => [
            'match_urls' => ['admin/promocode-list'],
            'module_names' => ['promotions', 'promocode-list'],
        ],
        'report-issue' => [
            'match_urls' => [
                'admin/report-issue/customer',
                'admin/report-issue/driver',
                'admin/report-issue/setting',
                'admin/report-issue/faqs/manage',
            ],
            'module_names' => ['customer-report-issues', 'driver-report-issues', 'report-issue-setting', 'faqs'],
            'name_patterns' => ['report issue'],
        ],
        'api-keys' => [
            'route_paths' => ['get:admin:api_keys'],
            'virtual' => true,
        ],
        'audit-logs' => [
            'route_paths' => ['get:admin:audit_logs', 'get:admin:audit_logs.export'],
            'virtual' => true,
        ],
        'security' => [
            'route_paths' => ['get:admin:security'],
            'virtual' => true,
        ],
    ];

    /** @var array<string, string> */
    private const ROUTE_TO_MATRIX_KEY = [
        'get:admin:dashboard' => 'dashboard',
        'post:admin:dashboard' => 'dashboard',
        'get:admin:security' => 'security',
        'get:admin:audit_logs' => 'audit-logs',
        'get:admin:audit_logs.export' => 'audit-logs',
        'get:admin:api_keys' => 'api-keys',
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
            ->where(function ($query) {
                $this->whereCsvContains($query, 'permissions', self::ACTION_VIEW);
            })
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
                $q->where('route_path', $routeName);
                $this->whereCsvContains($q, 'route_path_arr', $routeName, 'or');
            })
            ->first();

        if ($module === null) {
            $matrixKey = self::routeToMatrixKey($routeName);
            if ($matrixKey === 'dashboard') {
                return true;
            }
            if ($matrixKey !== null) {
                return $this->roleCanPerformRoute($role, $matrixKey, $routeName);
            }

            return in_array($routeName, ['get:admin:dashboard', 'post:admin:dashboard'], true);
        }

        $matrixKey = $this->matrixKeyForModule($module) ?? self::routeToMatrixKey($routeName);
        if ($matrixKey !== null && self::requiredActionForRoute($routeName) !== self::ACTION_VIEW) {
            return $this->roleCanPerformRoute($role, $matrixKey, $routeName);
        }

        if (RoleModulePermission::query()
            ->where('role_id', $role->id)
            ->where('module_id', $module->id)
            ->where(function ($query) {
                $this->whereCsvContains($query, 'permissions', self::ACTION_VIEW);
            })
            ->exists()) {
            return true;
        }

        $matrixKey = $this->matrixKeyForModule($module);

        return $matrixKey !== null && $this->roleHasMatrixKey($role, $matrixKey);
    }

    /** @return array<int, array{parent_menu: array, child_menu: array}> */
    public function buildMenuForAdmin(Admin $admin): array
    {
        $permittedIds = array_values(array_filter(
            $this->permittedModuleIds($admin),
            fn (int $moduleId): bool => $this->isGrantableModuleId($moduleId)
        ));
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

    /** @return array<string, list<string>> */
    public static function roleModuleMatrix(): array
    {
        return self::ROLE_MODULE_MATRIX;
    }

    /** @return list<string> */
    public static function allRoleSlugs(): array
    {
        return array_keys(self::ROLE_MODULE_MATRIX);
    }

    public static function isVirtualMatrixKey(string $matrixKey): bool
    {
        return ! empty(self::MATRIX_MODULE_RULES[$matrixKey]['virtual']);
    }

    public static function permissionCsvForRole(string $roleSlug): string
    {
        return self::ROLE_PERMISSION_CSV[$roleSlug] ?? '1,2,3';
    }

    /** @return list<string> */
    public static function permissionActionsForRole(string $roleSlug): array
    {
        return explode(',', self::permissionCsvForRole($roleSlug));
    }

    /** @return list<string> Matrix keys granted to a role (excluding admin_total star). */
    public function allowedMatrixKeysForRole(AdminRole $role): array
    {
        $keys = self::ROLE_MODULE_MATRIX[$role->slug] ?? [];
        if ($keys === ['*']) {
            return array_keys(array_filter(
                self::MATRIX_MODULE_RULES,
                static fn (array $rules): bool => empty($rules['virtual'])
            ));
        }

        return $keys;
    }

    /** @return list<string> */
    public function deniedMatrixKeysForRole(AdminRole $role): array
    {
        $allowed = $this->allowedMatrixKeysForRole($role);
        $denied = [];
        foreach (array_keys(self::MATRIX_MODULE_RULES) as $key) {
            if ($key === 'dashboard' || self::isVirtualMatrixKey($key)) {
                continue;
            }
            if (! in_array($key, $allowed, true)) {
                $denied[] = $key;
            }
        }

        return $denied;
    }

    public function roleHasMatrixKey(AdminRole $role, string $matrixKey): bool
    {
        $allowed = self::ROLE_MODULE_MATRIX[$role->slug] ?? [];
        if ($allowed === ['*']) {
            return true;
        }

        return in_array($matrixKey, $allowed, true);
    }

    /** @return list<string> */
    public function sampleRoutesForMatrixKey(string $matrixKey): array
    {
        if ($matrixKey === 'dashboard') {
            return ['get:admin:dashboard'];
        }
        $rules = self::MATRIX_MODULE_RULES[$matrixKey] ?? [];
        if (! empty($rules['virtual'])) {
            return $rules['route_paths'] ?? [];
        }
        $routes = [];
        $modules = AdminModule::query()->where('status', 1)->get();
        foreach ($this->moduleIdsForMatrixKey($matrixKey, $modules) as $moduleId) {
            $module = $modules->firstWhere('id', $moduleId);
            if ($module !== null && $module->route_path !== '') {
                $routes[] = $module->route_path;
            }
        }

        return array_values(array_unique($routes));
    }

    public static function routeToMatrixKey(?string $routeName): ?string
    {
        if ($routeName === null || $routeName === '') {
            return null;
        }

        if (isset(self::ROUTE_TO_MATRIX_KEY[$routeName])) {
            return self::ROUTE_TO_MATRIX_KEY[$routeName];
        }

        return self::inferRouteMatrixKey($routeName);
    }

    public static function requiredActionForRoute(string $routeName): string
    {
        if (preg_match('/delete_|_delete/i', $routeName)) {
            return self::ACTION_DELETE;
        }
        if (preg_match('/add_|_add/i', $routeName)) {
            return self::ACTION_CREATE;
        }
        if (preg_match('/approved_reject|approve/i', $routeName)) {
            return self::ACTION_APPROVE;
        }
        if (preg_match('/update_general_setting|app_version_setting|service_setting|vehicle_commission/i', $routeName)) {
            return self::ACTION_CONFIGURE;
        }
        if (preg_match('/update_|edit_|post:admin:/i', $routeName)) {
            return self::ACTION_EDIT;
        }

        return self::ACTION_VIEW;
    }

  /**
     * Map AJAX / mutation routes to RBAC matrix keys when not listed in admin_module.
     */
    private static function inferRouteMatrixKey(string $routeName): ?string
    {
        /** @var array<string, list<string>> */
        static $fragments = [
            'customer-list' => [
                'user_list', 'add_user', 'edit_user', 'delete_user', 'update_user_status',
                'update_customer_wallet', 'user_review', 'delete_user_review',
            ],
            'ride-list' => [
                'ride_list', 'ride_details', 'transport_update_ride', 'single_provider_ride',
            ],
            'transport-providers-list' => [
                'transport_service_provider', 'transport_service_driver', 'transport_provider',
                'transport_update_provider', 'add_transport_service_driver', 'edit_transport_service_driver',
                'transport_provider_document', 'edit_transport_provider_vehicle', 'delete_transport_provider',
            ],
            'pending-provider-list' => [
                'transport_service_un_approved', 'pending_transport', 'pending_provider',
                'update_approved_reject_provider_document',
            ],
            'earning-report' => ['earning_report'],
            'heat-map' => ['transport_heat_map'],
            'search-radius' => ['search_radius'],
            'geo-fencing-list' => ['restricted_area'],
            'cash-out-list' => ['transport_cash_out', 'cash_out'],
            'world-currency-list' => ['world_currency'],
            'vehicle-commission-rates' => ['vehicle_commission'],
            'site-setting' => ['general_setting', 'update_general_setting'],
            'app-version-setting' => ['app_version_setting', 'update_app_version'],
            'push-notification' => ['push_notification', 'delete_push_notification'],
            'support-page-list' => ['support_page', 'add_pages', 'edit_pages', 'delete_support_page'],
            'referral-history' => ['referral_list'],
            'report-issue' => ['report_issue', 'faqs'],
            'sos-list' => [':admin:sos'],
            'service-settings' => [
                'service_setting', 'vehicle_type', 'vehicle_service', 'required_document',
                'promocode', 'update_service_setting',
            ],
        ];

        foreach ($fragments as $matrixKey => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($routeName, $needle)) {
                    return $matrixKey;
                }
            }
        }

        return null;
    }

    private function roleCanPerformRoute(AdminRole $role, string $matrixKey, string $routeName): bool
    {
        if ($role->slug === 'admin_total') {
            return true;
        }
        if (! $this->roleHasMatrixKey($role, $matrixKey)) {
            return false;
        }

        return $this->roleHasActionOnMatrixKey($role, $matrixKey, self::requiredActionForRoute($routeName));
    }

    private function roleHasActionOnMatrixKey(AdminRole $role, string $matrixKey, string $action): bool
    {
        if ($role->slug === 'admin_total') {
            return true;
        }

        $moduleIds = $this->moduleIdsForMatrixKey($matrixKey);
        if ($moduleIds === []) {
            return $this->roleHasMatrixKey($role, $matrixKey);
        }

        return RoleModulePermission::query()
            ->where('role_id', $role->id)
            ->whereIn('module_id', $moduleIds)
            ->where(function ($query) use ($action) {
                $this->whereCsvContains($query, 'permissions', $action);
            })
            ->exists();
    }

    public function canPerformAction(Admin $admin, string $matrixKey, string $action): bool
    {
        $role = $this->resolveRole($admin);
        if ($role === null) {
            return (int) $admin->roles === 1;
        }

        return $this->roleHasActionOnMatrixKey($role, $matrixKey, $action);
    }

    /**
     * @return list<int> Module IDs (including parents) that satisfy a matrix key.
     */
    public function moduleIdsForMatrixKey(string $matrixKey, ?Collection $modules = null): array
    {
        $rules = self::MATRIX_MODULE_RULES[$matrixKey] ?? null;
        if ($rules === null) {
            return [];
        }
        if (! empty($rules['virtual'])) {
            return [];
        }

        $modules = $modules ?? AdminModule::query()->where('status', 1)->get();
        $ids = [];
        foreach ($modules as $module) {
            if (! $this->moduleMatchesRules($module, $rules) || ! $this->isGrantableModule($module)) {
                continue;
            }
            $ids[] = (int) $module->id;
            if ((int) $module->parent_id > 0) {
                $parent = $modules->firstWhere('id', (int) $module->parent_id);
                if ($parent !== null && ! $this->isStructuralParentModule($parent, $matrixKey)) {
                    $ids[] = (int) $module->parent_id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    public function matrixKeyForModule(AdminModule $module): ?string
    {
        foreach (self::MATRIX_MODULE_RULES as $key => $rules) {
            if (! empty($rules['virtual'])) {
                continue;
            }
            if ($this->moduleMatchesRules($module, $rules)) {
                return $key;
            }
        }

        return self::normalizeModuleKey($module->module_name, $module->route_path, $module->match_url);
    }

    /** @return list<string> */
    public function matrixKeysForRole(AdminRole $role): array
    {
        $keys = self::ROLE_MODULE_MATRIX[$role->slug] ?? [];
        if ($keys === ['*']) {
            return array_keys(self::MATRIX_MODULE_RULES);
        }

        return $keys;
    }

    /** @return list<string> Matrix keys with no matching active module (excluding virtual). */
    public function unresolvedMatrixKeys(): array
    {
        $modules = AdminModule::query()->where('status', 1)->get();
        $missing = [];
        foreach (self::MATRIX_MODULE_RULES as $key => $rules) {
            if (! empty($rules['virtual']) || in_array($key, self::DEFERRED_MATRIX_KEYS, true)) {
                continue;
            }
            if ($this->moduleIdsForMatrixKey($key, $modules) === []) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    public static function normalizeModuleKey(?string $moduleName, ?string $routePath, ?string $matchUrl = null): string
    {
        if ($matchUrl !== null && $matchUrl !== '') {
            $fromUrl = self::matrixKeyFromMatchUrl($matchUrl);
            if ($fromUrl !== null) {
                return $fromUrl;
            }
        }

        $key = trim((string) $moduleName);
        if ($key === '' && $routePath !== null) {
            $key = str_replace(['get:admin:', 'post:admin:'], '', $routePath);
        }
        $key = strtolower(str_replace(['/', '_', ' '], '-', $key));

        return match ($key) {
            'transprort-providers-list' => 'transport-providers-list',
            'restricted-area-list' => 'geo-fencing-list',
            'earning-report' => 'earning-report',
            'rides' => 'ride-list',
            default => $key,
        };
    }

    public function seedRolePermissions(): void
    {
        if (! Schema::hasTable('admin_roles') || ! Schema::hasTable('role_module_permissions')) {
            return;
        }

        $modules = AdminModule::query()->where('status', 1)->get();

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

            $grantedModuleIds = [];
            foreach ($moduleKeys as $key) {
                if ($key === 'dashboard') {
                    continue;
                }
                $perms = self::permissionCsvForRole($roleSlug);
                foreach ($this->moduleIdsForMatrixKey($key, $modules) as $moduleId) {
                    if (isset($grantedModuleIds[$moduleId])) {
                        continue;
                    }
                    $grantedModuleIds[$moduleId] = true;
                    $this->grant($role->id, $moduleId, $perms);
                }
            }
        }
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

    private function moduleMatchesRules(AdminModule $module, array $rules): bool
    {
        $matchUrl = trim((string) $module->match_url);
        $moduleName = strtolower(trim((string) $module->module_name));
        $routePath = trim((string) $module->route_path);
        $exclude = $rules['exclude_match_urls'] ?? [];
        $excludeNames = $rules['exclude_module_names'] ?? [];

        if ($moduleName !== '' && in_array($moduleName, array_map('strtolower', $excludeNames), true)) {
            return false;
        }

        if ($matchUrl !== '' && in_array($matchUrl, $exclude, true)) {
            return false;
        }

        foreach ($rules['match_urls'] ?? [] as $pattern) {
            if ($matchUrl === $pattern || ($matchUrl !== '' && str_ends_with($matchUrl, $pattern))) {
                return true;
            }
        }

        foreach ($rules['name_patterns'] ?? [] as $pattern) {
            if (str_contains(strtolower($module->name), strtolower($pattern))) {
                return true;
            }
        }

        foreach ($rules['module_names'] ?? [] as $name) {
            if ($moduleName === strtolower($name)) {
                return true;
            }
        }

        foreach ($rules['route_paths'] ?? [] as $routeName) {
            if ($routePath === $routeName) {
                return true;
            }
        }

        return false;
    }

    private function isStructuralParentModule(AdminModule $parent, string $matrixKey): bool
    {
        if (strtolower((string) $parent->module_name) !== 'service settings') {
            return false;
        }

        return in_array($matrixKey, ['driver-documents', 'promotions'], true);
    }

    private function isGrantableModule(AdminModule $module): bool
    {
        if ($module->route_path === '') {
            return true;
        }

        return Route::has($module->route_path);
    }

    private function isGrantableModuleId(int $moduleId): bool
    {
        $module = AdminModule::query()->find($moduleId);

        return $module !== null && $this->isGrantableModule($module);
    }

    private static function matrixKeyFromMatchUrl(string $matchUrl): ?string
    {
        $normalized = trim($matchUrl, '/');
        foreach (self::MATRIX_MODULE_RULES as $key => $rules) {
            if (! empty($rules['virtual'])) {
                continue;
            }
            foreach ($rules['match_urls'] ?? [] as $pattern) {
                $pattern = trim($pattern, '/');
                if ($normalized === $pattern || str_ends_with($normalized, $pattern)) {
                    return $key;
                }
            }
        }

        return null;
    }

    private function grant(int $roleId, int $moduleId, string $permissions): void
    {
        RoleModulePermission::query()->updateOrCreate(
            ['role_id' => $roleId, 'module_id' => $moduleId],
            ['permissions' => $permissions]
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     */
    private function whereCsvContains($query, string $column, string $value, string $boolean = 'and'): void
    {
        $method = $boolean === 'or' ? 'orWhere' : 'where';
        if (DB::connection()->getDriverName() === 'sqlite') {
            $query->{$method}(function ($inner) use ($column, $value) {
                $inner->where($column, $value)
                    ->orWhere($column, 'like', $value.',%')
                    ->orWhere($column, 'like', '%,'.$value.',%')
                    ->orWhere($column, 'like', '%,'.$value);
            });

            return;
        }

        if ($boolean === 'or') {
            $query->orWhereRaw("find_in_set(?, {$column})", [$value]);
        } else {
            $query->whereRaw("find_in_set(?, {$column})", [$value]);
        }
    }
}
