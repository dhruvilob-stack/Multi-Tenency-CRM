<?php

namespace App\Filament\Organization\Widgets;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\OrganizationRevenueSnapshot;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrganizationPerformanceStats extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $revenueCents = OrganizationRevenueSnapshot::query()
            ->where('recorded_at', '>=', now()->subDays(30))
            ->sum('revenue_cents');

        $organizationId = auth()->user()?->organization_id;

        $openInvoices = Invoice::query()
            ->whereIn('status', ['pending', 'overdue'])
            ->when($organizationId, function ($query, int $organizationId) {
                $query->whereHas('purchaseOrder', function ($poQuery) use ($organizationId): void {
                    $poQuery->where('buyer_id', $organizationId);
                });
            })
            ->count();

        $purchaseOrders = PurchaseOrder::query()
            ->when($organizationId, fn ($query, int $organizationId) => $query->where('buyer_id', $organizationId))
            ->count();

        return [
            Stat::make('Total Products', Product::query()->count())
                ->icon('heroicon-m-cube')
                ->color('warning')
                ->description('Active catalog items'),

            Stat::make('Total Brands', Brand::query()->count())
                ->icon('heroicon-m-tag')
                ->color('primary'),

            Stat::make('Total Categories', Category::query()->count())
                ->icon('heroicon-m-squares-2x2')
                ->color('info'),

            Stat::make('Suppliers', Supplier::query()->count())
                ->icon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Inventory Items', InventoryItem::query()->count())
                ->icon('heroicon-m-archive-box')
                ->color('success'),

            Stat::make('Purchase Orders', $purchaseOrders)
                ->icon('heroicon-m-clipboard-document-check')
                ->color('danger'),

            Stat::make('Open Invoices', $openInvoices)
                ->icon('heroicon-m-receipt-refund')
                ->color('warning'),

            Stat::make('Revenue (30 days)', '$'.number_format($revenueCents / 100, 2))
                ->icon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('dashboard.organization_performance');
    }
}
