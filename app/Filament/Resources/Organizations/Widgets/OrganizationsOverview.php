<?php

namespace App\Filament\Resources\Organizations\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\Organization;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrganizationsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = Organization::query();

        $total = (clone $query)->count();
        $active = (clone $query)->where('status', 'active')->count();
        $buyers = (clone $query)->where('type', 'buyer')->count();

        return [
            Stat::make('Organizations', $total)
                ->icon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Active', $active)
                ->icon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Buyers', $buyers)
                ->icon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
