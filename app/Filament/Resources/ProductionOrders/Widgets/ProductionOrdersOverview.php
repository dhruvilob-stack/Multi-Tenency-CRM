<?php

namespace App\Filament\Resources\ProductionOrders\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\ProductionOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductionOrdersOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = ProductionOrder::query();

        $total = (clone $query)->count();
        $active = (clone $query)->where('status', '!=', 'completed')->count();
        $avgCostPerUnitCents = (int) round((clone $query)->avg('cost_per_unit_cents') ?? 0);

        return [
            Stat::make('Production orders', $total)
                ->icon('heroicon-m-cog-6-tooth')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Active', $active)
                ->icon('heroicon-m-bolt')
                ->color('success')
                ->chart($this->chartDailyCount((clone $query)->where('status', '!=', 'completed'), days: 7)),

            Stat::make('Avg cost / unit', $this->formatUsdCents($avgCostPerUnitCents))
                ->icon('heroicon-m-banknotes')
                ->color('warning')
                ->chart($this->chartRecentInt((clone $query), 'cost_per_unit_cents', limit: 10)),
        ];
    }
}
