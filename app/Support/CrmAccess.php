<?php

namespace App\Support;

use App\Models\User;

class CrmAccess
{
    /**
     * @var array<string, string>
     */
    private const MODULE_LABELS = [
        'welcome' => 'Welcome',
        'dashboard' => 'Dashboard',
        'manufacturer_dashboard' => 'Manufacturer Dashboard',
        'organization_dashboard' => 'Organization Dashboard',
        'supplier_dashboard' => 'Supplier Dashboard',
        'manufacturing' => 'Manufacturer / Manufacturing',
        'products' => 'Manufacturer / Catalog / Products',
        'inventory' => 'Manufacturer / Inventory / Inventory Items',
        'orders' => 'Manufacturer / Orders / RFQs',
        'quality_reports' => 'Manufacturer / Quality / Quality Reports',
        'logistics' => 'Manufacturer / Logistics / Dispatch Orders',
        'purchase_orders' => 'Manufacturer / Procurement / Purchase Orders',
        'suppliers' => 'Manufacturer / Partners / Suppliers',
        'organizations' => 'Manufacturer / Manufacturer Org. / Organizations',
        'reports' => 'Performance Reports',
        'users' => 'Authorization Management',
        'org_catalog' => 'Organization / Catalog',
        'org_operations' => 'Organization / Operations',
        'org_warehouse' => 'Organization / Warehouse',
        'org_finance' => 'Organization / Finance',
        'org_partners' => 'Organization / Partners',
        'org_access' => 'Organization / Access',
        'supplier_finance' => 'Supplier / Finance',
        'supplier_operations' => 'Supplier / Operations',
        'supplier_sales' => 'Supplier / Sales',
        'supplier_logistics' => 'Supplier / Logistics',
    ];

    /**
     * @var array<string, array<string, array<string, bool>>>
     */
    private const MATRIX = [
        'manufacturer' => [
            'welcome' => ['view' => true, 'manage' => false],
            'dashboard' => ['view' => true, 'manage' => false],
            'manufacturer_dashboard' => ['view' => true, 'manage' => false],
            'organization_dashboard' => ['view' => false, 'manage' => false],
            'supplier_dashboard' => ['view' => false, 'manage' => false],
            'manufacturing' => ['view' => true, 'manage' => true],
            'products' => ['view' => true, 'manage' => true],
            'inventory' => ['view' => true, 'manage' => true],
            'orders' => ['view' => true, 'manage' => true],
            'quality_reports' => ['view' => true, 'manage' => true],
            'logistics' => ['view' => true, 'manage' => true],
            'purchase_orders' => ['view' => true, 'manage' => true],
            'suppliers' => ['view' => true, 'manage' => false],
            'organizations' => ['view' => true, 'manage' => false],
            'reports' => ['view' => true, 'manage' => false],
            'users' => ['view' => false, 'manage' => false],
            'org_catalog' => ['view' => false, 'manage' => false],
            'org_operations' => ['view' => false, 'manage' => false],
            'org_warehouse' => ['view' => false, 'manage' => false],
            'org_finance' => ['view' => false, 'manage' => false],
            'org_partners' => ['view' => false, 'manage' => false],
            'org_access' => ['view' => false, 'manage' => false],
            'supplier_finance' => ['view' => false, 'manage' => false],
            'supplier_operations' => ['view' => false, 'manage' => false],
            'supplier_sales' => ['view' => false, 'manage' => false],
            'supplier_logistics' => ['view' => false, 'manage' => false],
        ],
        'super_admin' => [
            'welcome' => ['view' => true, 'manage' => false],
            'dashboard' => ['view' => true, 'manage' => false],
            'manufacturer_dashboard' => ['view' => true, 'manage' => false],
            'organization_dashboard' => ['view' => true, 'manage' => false],
            'supplier_dashboard' => ['view' => true, 'manage' => false],
            'manufacturing' => ['view' => true, 'manage' => true],
            'products' => ['view' => true, 'manage' => true],
            'inventory' => ['view' => true, 'manage' => true],
            'orders' => ['view' => true, 'manage' => true],
            'purchase_orders' => ['view' => true, 'manage' => true],
            'quality_reports' => ['view' => true, 'manage' => true],
            'logistics' => ['view' => true, 'manage' => true],
            'suppliers' => ['view' => true, 'manage' => true],
            'organizations' => ['view' => true, 'manage' => true],
            'reports' => ['view' => true, 'manage' => true],
            'users' => ['view' => true, 'manage' => true],
            'org_catalog' => ['view' => true, 'manage' => true],
            'org_operations' => ['view' => true, 'manage' => true],
            'org_warehouse' => ['view' => true, 'manage' => true],
            'org_finance' => ['view' => true, 'manage' => true],
            'org_partners' => ['view' => true, 'manage' => true],
            'org_access' => ['view' => true, 'manage' => true],
            'supplier_finance' => ['view' => true, 'manage' => true],
            'supplier_operations' => ['view' => true, 'manage' => true],
            'supplier_sales' => ['view' => true, 'manage' => true],
            'supplier_logistics' => ['view' => true, 'manage' => true],
        ],
        'organization_admin' => [
            'welcome' => ['view' => true, 'manage' => false],
            'dashboard' => ['view' => true, 'manage' => false],
            'manufacturer_dashboard' => ['view' => false, 'manage' => false],
            'organization_dashboard' => ['view' => true, 'manage' => false],
            'supplier_dashboard' => ['view' => false, 'manage' => false],
            'orders' => ['view' => true, 'manage' => true],
            'purchase_orders' => ['view' => true, 'manage' => true],
            'organizations' => ['view' => true, 'manage' => false],
            'reports' => ['view' => true, 'manage' => false],
            'users' => ['view' => false, 'manage' => false],
            'manufacturing' => ['view' => false, 'manage' => false],
            'products' => ['view' => false, 'manage' => false],
            'inventory' => ['view' => false, 'manage' => false],
            'quality_reports' => ['view' => false, 'manage' => false],
            'logistics' => ['view' => false, 'manage' => false],
            'suppliers' => ['view' => false, 'manage' => false],
            'org_catalog' => ['view' => true, 'manage' => true],
            'org_operations' => ['view' => true, 'manage' => true],
            'org_warehouse' => ['view' => true, 'manage' => true],
            'org_finance' => ['view' => true, 'manage' => true],
            'org_partners' => ['view' => true, 'manage' => true],
            'org_access' => ['view' => true, 'manage' => true],
            'supplier_finance' => ['view' => false, 'manage' => false],
            'supplier_operations' => ['view' => false, 'manage' => false],
            'supplier_sales' => ['view' => false, 'manage' => false],
            'supplier_logistics' => ['view' => false, 'manage' => false],
        ],
        'supplier' => [
            'welcome' => ['view' => true, 'manage' => false],
            'dashboard' => ['view' => true, 'manage' => false],
            'manufacturer_dashboard' => ['view' => false, 'manage' => false],
            'organization_dashboard' => ['view' => false, 'manage' => false],
            'supplier_dashboard' => ['view' => true, 'manage' => false],
            'purchase_orders' => ['view' => true, 'manage' => false],
            'logistics' => ['view' => true, 'manage' => true],
            'inventory' => ['view' => true, 'manage' => false],
            'reports' => ['view' => true, 'manage' => false],
            'orders' => ['view' => false, 'manage' => false],
            'organizations' => ['view' => false, 'manage' => false],
            'suppliers' => ['view' => false, 'manage' => false],
            'manufacturing' => ['view' => false, 'manage' => false],
            'products' => ['view' => false, 'manage' => false],
            'quality_reports' => ['view' => false, 'manage' => false],
            'users' => ['view' => false, 'manage' => false],
            'org_catalog' => ['view' => false, 'manage' => false],
            'org_operations' => ['view' => false, 'manage' => false],
            'org_warehouse' => ['view' => false, 'manage' => false],
            'org_finance' => ['view' => false, 'manage' => false],
            'org_partners' => ['view' => false, 'manage' => false],
            'org_access' => ['view' => false, 'manage' => false],
            'supplier_finance' => ['view' => true, 'manage' => true],
            'supplier_operations' => ['view' => true, 'manage' => false],
            'supplier_sales' => ['view' => true, 'manage' => true],
            'supplier_logistics' => ['view' => true, 'manage' => true],
        ],
    ];

    public static function canView(string $module): bool
    {
        $permission = static::modulePermission($module);

        return $permission['view'] ?? false;
    }

    public static function canViewForUser(User $user, string $module): bool
    {
        $permission = static::modulePermissionForUser($user, $module);

        return $permission['view'] ?? false;
    }

    public static function canManage(string $module): bool
    {
        $permission = static::modulePermission($module);

        return $permission['manage'] ?? false;
    }

    public static function canManageForUser(User $user, string $module): bool
    {
        $permission = static::modulePermissionForUser($user, $module);

        return $permission['manage'] ?? false;
    }

    /**
     * @return array<int, string>
     */
    public static function definedModules(): array
    {
        return array_keys(self::MODULE_LABELS);
    }

    /**
     * @return array<string, string>
     */
    public static function permissionOptions(): array
    {
        $options = [];

        foreach (self::MODULE_LABELS as $module => $label) {
            $options["{$module}.view"] = "{$label} - View";
            $options["{$module}.manage"] = "{$label} - Manage";
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public static function permissionsForRole(string $role): array
    {
        if (! isset(self::MATRIX[$role])) {
            return [];
        }

        $permissions = [];

        foreach (self::MATRIX[$role] as $module => $access) {
            if ($access['view'] ?? false) {
                $permissions[] = "{$module}.view";
            }

            if ($access['manage'] ?? false) {
                $permissions[] = "{$module}.manage";
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * @return array<string, bool>
     */
    private static function modulePermission(string $module): array
    {
        $user = auth()->user();

        if (! $user instanceof User || ! $user->is_active) {
            return [];
        }

        return static::modulePermissionForUser($user, $module);
    }

    /**
     * @return array<string, bool>
     */
    private static function modulePermissionForUser(User $user, string $module): array
    {
        $customPermissions = static::resolveCustomPermissions($user);

        if ($customPermissions !== []) {
            $managePermission = "{$module}.manage";
            $viewPermission = "{$module}.view";

            $canManage = in_array($managePermission, $customPermissions, true);
            $canView = $canManage || in_array($viewPermission, $customPermissions, true);

            return [
                'view' => $canView,
                'manage' => $canManage,
            ];
        }

        $role = static::resolveRoleName($user);

        if (! is_string($role) || ! isset(static::MATRIX[$role])) {
            return [];
        }

        return static::MATRIX[$role][$module] ?? [];
    }

    private static function resolveRoleName(User $user): ?string
    {
        $role = $user->role?->name ?? $user->role;

        if (! is_string($role)) {
            return null;
        }

        return $role;
    }

    /**
     * @return array<int, string>
     */
    private static function resolveCustomPermissions(User $user): array
    {
        if (! is_array($user->permissions)) {
            return [];
        }

        return array_values(array_filter(
            $user->permissions,
            fn (mixed $permission): bool => is_string($permission) && $permission !== ''
        ));
    }
}
