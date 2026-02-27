<?php

namespace App\Filament\Organization\Resources\InventoryItems\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Organization\Resources\InventoryItems\InventoryItemResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = InventoryItemResource::getEloquentQuery();

        $totalItems = (clone $query)->count();
        $totalQuantityOnHand = (int) ((clone $query)->sum('quantity_on_hand') ?? 0);
        $lowStock = (clone $query)
            ->whereNotNull('reorder_threshold')
            ->whereColumn('quantity_on_hand', '<=', 'reorder_threshold')
            ->count();

        return [
            Stat::make('Inventory items', $totalItems)
                ->icon('heroicon-m-archive-box')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Qty on hand', number_format($totalQuantityOnHand))
                ->icon('heroicon-m-squares-plus')
                ->color('info'),

            Stat::make('Low stock', $lowStock)
                ->icon('heroicon-m-fire')
                ->color($lowStock > 0 ? 'danger' : 'success'),
        ];
    }
}
