<?php

namespace App\Filament\Pages\Concerns;

use App\Support\CrmAccess;

trait AuthorizesCrmPage
{
    public static function canAccess(): bool
    {
        return CrmAccess::canView(static::crmModule());
    }

    private static function crmModule(): string
    {
        if (property_exists(static::class, 'crmModule')) {
            return static::$crmModule;
        }

        return 'dashboard';
    }
}
