<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\OrganizationRevenueSnapshot;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\QualityReport;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminPerformanceStats extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $revenueCents = OrganizationRevenueSnapshot::query()
            ->where('recorded_at', '>=', now()->subDays(30))
            ->sum('revenue_cents');

        return [
            Stat::make('Organizations', Organization::query()->count())
                ->icon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Suppliers', Supplier::query()->count())
                ->icon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Products', Product::query()->count())
                ->icon('heroicon-m-cube')
                ->color('warning'),

            Stat::make('Inventory Items', InventoryItem::query()->count())
                ->icon('heroicon-m-archive-box')
                ->color('success'),

            Stat::make('Production Orders', ProductionOrder::query()->count())
                ->icon('heroicon-m-cog-6-tooth')
                ->color('gray'),

            Stat::make('Open POs', PurchaseOrder::query()->where('status', '!=', 'completed')->count())
                ->icon('heroicon-m-clipboard-document-check')
                ->color('danger'),

            Stat::make('Quality Reports', QualityReport::query()->count())
                ->icon('heroicon-m-shield-check')
                ->color('info'),

            Stat::make('Revenue (30 days)', '$'.number_format($revenueCents / 100, 2))
                ->icon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('dashboard.system_performance');
    }
}
