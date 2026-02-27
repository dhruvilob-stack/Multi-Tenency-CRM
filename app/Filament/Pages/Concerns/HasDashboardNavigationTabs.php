<?php

namespace App\Filament\Pages\Concerns;

use App\Filament\Pages\Welcome;
use App\Support\DashboardNavigationItems;

trait HasDashboardNavigationTabs
{
    public static function getNavigationItems(): array
    {
        if (static::class === Welcome::class) {
            return parent::getNavigationItems();
        }

        $dashboardGroup = static::getNavigationGroup();
        $dashboardLabel = static::getNavigationLabel();

        $tabItems = array_map(
            fn ($item) => $item
                ->group($dashboardGroup)
                ->parentItem($dashboardLabel),
            DashboardNavigationItems::forPage(static::class),
        );

        return [
            ...parent::getNavigationItems(),
            ...$tabItems,
        ];
    }
}
