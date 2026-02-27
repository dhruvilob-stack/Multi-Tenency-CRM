<?php

namespace App\Filament\Pages;

use App\Filament\Organization\Widgets\OrganizationPerformanceStats;
use App\Filament\Organization\Widgets\OrganizationsDetailsTable;
use App\Filament\Organization\Widgets\PurchaseOrdersOverview;
use App\Filament\Pages\Concerns\AuthorizesCrmPage;
use App\Filament\Pages\Concerns\HasDashboardNavigationTabs;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class OrganizationDashboard extends Dashboard
{
    use AuthorizesCrmPage;
    use HasDashboardNavigationTabs;

    protected static string $crmModule = 'organization_dashboard';

    protected static string $routePath = 'organization-dashboard';

    protected static ?string $title = 'Organization Dashboard';

    protected static ?string $navigationLabel = 'Organization Dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 3;

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            OrganizationPerformanceStats::class,
            PurchaseOrdersOverview::class,
            OrganizationsDetailsTable::class,
        ];
    }
}
