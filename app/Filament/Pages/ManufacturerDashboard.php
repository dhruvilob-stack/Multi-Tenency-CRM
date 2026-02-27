<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesCrmPage;
use App\Filament\Pages\Concerns\HasDashboardNavigationTabs;
use App\Filament\Widgets\ManufacturerDetailsTable;
use App\Filament\Widgets\PurchaseOrdersOverview;
use App\Filament\Widgets\SuperAdminPerformanceStats;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class ManufacturerDashboard extends Dashboard
{
    use AuthorizesCrmPage;
    use HasDashboardNavigationTabs;

    protected static string $crmModule = 'manufacturer_dashboard';

    protected static string $routePath = 'manufacturer-dashboard';

    protected static ?string $title = 'Manufacturer Dashboard';

    protected static ?string $navigationLabel = 'Manufacturer Dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 2;

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            SuperAdminPerformanceStats::class,
            PurchaseOrdersOverview::class,
            ManufacturerDetailsTable::class,
        ];
    }
}
