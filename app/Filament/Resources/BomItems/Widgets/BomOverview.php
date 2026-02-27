<?php

namespace App\Filament\Resources\BomItems\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\BomItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BomOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = BomItem::query();

        $total = (clone $query)->count();
        $products = (clone $query)->distinct()->count('product_id');
        $avgUnitCostCents = (int) round((clone $query)->avg('unit_cost_cents') ?? 0);

        return [
            Stat::make('BOM items', $total)
                ->icon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Products covered', $products)
                ->icon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Avg unit cost', $this->formatUsdCents($avgUnitCostCents))
                ->icon('heroicon-m-banknotes')
                ->color('warning')
                ->chart($this->chartRecentInt((clone $query), 'unit_cost_cents', limit: 10)),
        ];
    }
}
