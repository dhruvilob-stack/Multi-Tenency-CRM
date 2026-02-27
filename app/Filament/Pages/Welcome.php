<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesCrmPage;
use App\Filament\Pages\Concerns\HasDashboardNavigationTabs;
use App\Filament\Widgets\PurchaseOrdersOverview;
use App\Filament\Widgets\SuperAdminPerformanceStats;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Welcome extends Dashboard
{
    use AuthorizesCrmPage;
    use HasDashboardNavigationTabs;

    protected static string $crmModule = 'welcome';

    protected static string $routePath = '/';

    protected static ?string $title = 'Welcome';

    protected static ?string $navigationLabel = 'Welcome';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            SuperAdminPerformanceStats::class,
            PurchaseOrdersOverview::class,
        ];
    }
}
