<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesCrmPage;
use App\Filament\Pages\Concerns\HasDashboardNavigationTabs;
use App\Filament\Supplier\Widgets\PurchaseOrdersOverview;
use App\Filament\Supplier\Widgets\SupplierPerformanceStats;
use App\Filament\Supplier\Widgets\SuppliersDetailsTable;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class SupplierDashboard extends Dashboard
{
    use AuthorizesCrmPage;
    use HasDashboardNavigationTabs;

    protected static string $crmModule = 'supplier_dashboard';

    protected static string $routePath = 'supplier-dashboard';

    protected static ?string $title = 'Supplier Dashboard';

    protected static ?string $navigationLabel = 'Supplier Dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 4;

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            SupplierPerformanceStats::class,
            PurchaseOrdersOverview::class,
            SuppliersDetailsTable::class,
        ];
    }
}
