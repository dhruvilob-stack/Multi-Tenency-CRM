<?php

namespace App\Filament\Resources\DispatchOrders\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\DispatchOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DispatchOrdersOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = DispatchOrder::query();

        $total = (clone $query)->count();
        $draft = (clone $query)->where('status', 'draft')->count();
        $dispatched = (clone $query)->where('status', 'dispatched')->count();

        return [
            Stat::make('Dispatch orders', $total)
                ->icon('heroicon-m-paper-airplane')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Draft', $draft)
                ->icon('heroicon-m-pencil-square')
                ->color('warning'),

            Stat::make('Dispatched', $dispatched)
                ->icon('heroicon-m-truck')
                ->color('success'),
        ];
    }
}
