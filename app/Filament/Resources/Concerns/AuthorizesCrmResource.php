<?php

namespace App\Filament\Resources\Concerns;

use App\Support\CrmAccess;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesCrmResource
{
    public static function canAccess(): bool
    {
        return CrmAccess::canView(static::crmModule());
    }

    public static function canCreate(): bool
    {
        return static::canManageResource();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canManageResource();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canManageResource();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManageResource();
    }

    private static function canManageResource(): bool
    {
        if (static::isCrmViewOnly()) {
            return false;
        }

        return CrmAccess::canManage(static::crmModule());
    }

    private static function crmModule(): string
    {
        if (property_exists(static::class, 'crmModule')) {
            return static::$crmModule;
        }

        return 'dashboard';
    }

    private static function isCrmViewOnly(): bool
    {
        if (property_exists(static::class, 'crmViewOnly')) {
            return static::$crmViewOnly;
        }

        return false;
    }
}
