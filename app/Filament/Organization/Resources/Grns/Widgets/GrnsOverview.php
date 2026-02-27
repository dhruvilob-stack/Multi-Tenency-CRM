<?php

namespace App\Filament\Organization\Resources\Grns\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Organization\Resources\Grns\GrnResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GrnsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = GrnResource::getEloquentQuery();

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $received = (clone $query)->where('status', 'received')->count();

        return [
            Stat::make('GRNs', $total)
                ->icon('heroicon-m-inbox-arrow-down')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Pending', $pending)
                ->icon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Received', $received)
                ->icon('heroicon-m-sparkles')
                ->color('success'),
        ];
    }
}
