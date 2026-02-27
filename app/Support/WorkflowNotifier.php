<?php

namespace App\Support;

use App\Events\DatabaseNotificationsSentNow;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class WorkflowNotifier
{
    /**
     * @var array<string, true>
     */
    private static array $sentFingerprints = [];

    public static function notifyUsers(iterable $users, string $title, string $body, ?string $url = null, ?string $section = null, string|array|null $permissionModules = null): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $normalizedPermissionModules = static::normalizePermissionModules($permissionModules, $section);

        foreach ($users as $user) {
            if (! $user instanceof User) {
                continue;
            }

            if (! $user->is_active) {
                continue;
            }

            if ($normalizedPermissionModules !== [] && ! static::userCanAccessAnyModule($user, $normalizedPermissionModules)) {
                continue;
            }

            $fingerprint = sha1(implode('|', [
                app()->bound('request') ? (string) spl_object_id(request()) : 'no-request',
                (string) $user->getKey(),
                $title,
                $body,
                (string) $url,
                (string) $section,
            ]));

            if (isset(self::$sentFingerprints[$fingerprint])) {
                continue;
            }

            self::$sentFingerprints[$fingerprint] = true;

            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-s-bell')
                ->color('info');

            if ($url) {
                $notification->actions([
                    Action::make('view')
                        ->label('View')
                        ->button()
                        ->url($url),
                ]);
            }

            if ($section) {
                $notification->viewData([
                    'section' => $section,
                ]);
            }

            $notification
                ->sendToDatabase($user)
                ->broadcast($user);

            event(new DatabaseNotificationsSentNow($user));
        }
    }

    public static function notifyOrganizationAdmins(int $organizationId, string $title, string $body, ?string $url = null, ?string $section = null, string|array|null $permissionModules = null): void
    {
        $users = User::query()
            ->where('organization_id', $organizationId)
            ->where(function ($query): void {
                $query->where('role', 'organization_admin')
                    ->orWhereHas('role', fn ($roleQuery) => $roleQuery->where('name', 'organization_admin'));
            })
            ->get();

        self::notifyUsers($users, $title, $body, $url, $section, $permissionModules);
    }

    public static function notifySuperAdmins(string $title, string $body, ?string $url = null, ?string $section = null, string|array|null $permissionModules = null): void
    {
        $users = User::query()
            ->where(function ($query): void {
                $query->where('role', 'super_admin')
                    ->orWhereHas('role', fn ($roleQuery) => $roleQuery->where('name', 'super_admin'));
            })
            ->get();

        self::notifyUsers($users, $title, $body, $url, $section, $permissionModules);
    }

    public static function notifyManufacturerAdmins(string $title, string $body, ?string $url = null, ?string $section = null, string|array|null $permissionModules = null): void
    {
        $users = User::query()
            ->where(function ($query): void {
                $query->where('role', 'manufacturer')
                    ->orWhereHas('role', fn ($roleQuery) => $roleQuery->where('name', 'manufacturer'))
                    ->orWhere(function ($orgAdminQuery): void {
                        $orgAdminQuery
                            ->whereHas('organization', fn ($organizationQuery) => $organizationQuery->where('type', 'manufacturer'))
                            ->where(function ($roleQuery): void {
                                $roleQuery->where('role', 'organization_admin')
                                    ->orWhereHas('role', fn ($nestedRoleQuery) => $nestedRoleQuery->where('name', 'organization_admin'));
                            });
                    });
            })
            ->get();

        self::notifyUsers($users, $title, $body, $url, $section, $permissionModules);
    }

    public static function notifySupplierUser(int $supplierId, string $title, string $body, ?string $url = null, ?string $section = null, string|array|null $permissionModules = null): void
    {
        $users = User::query()
            ->whereHas('supplier', fn ($query) => $query->where('id', $supplierId))
            ->get();

        self::notifyUsers($users, $title, $body, $url, $section, $permissionModules);
    }

    /**
     * @return array<int, string>
     */
    private static function normalizePermissionModules(string|array|null $permissionModules, ?string $section): array
    {
        if (is_string($permissionModules) && $permissionModules !== '') {
            return [$permissionModules];
        }

        if (is_array($permissionModules)) {
            return array_values(array_unique(array_filter(
                $permissionModules,
                fn (mixed $module): bool => is_string($module) && $module !== '',
            )));
        }

        return match ($section) {
            'catalog' => ['products', 'suppliers', 'organizations', 'org_catalog', 'org_partners'],
            'orders' => ['orders', 'purchase_orders', 'inventory', 'logistics', 'org_operations', 'org_warehouse', 'org_finance', 'supplier_operations', 'supplier_sales', 'supplier_finance', 'supplier_logistics'],
            default => [],
        };
    }

    /**
     * @param  array<int, string>  $modules
     */
    private static function userCanAccessAnyModule(User $user, array $modules): bool
    {
        foreach ($modules as $module) {
            if (CrmAccess::canViewForUser($user, $module)) {
                return true;
            }
        }

        return false;
    }
}
