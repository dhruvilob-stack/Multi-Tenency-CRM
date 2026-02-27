<?php

namespace App\Filament\Resources\Suppliers\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuppliersOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = Supplier::query();

        $totalSuppliers = (clone $query)->count();
        $activeSuppliers = (clone $query)->where('status', 'active')->count();
        $invitedSuppliers = (clone $query)->where('status', 'invited')->count();

        return [
            Stat::make('Suppliers', $totalSuppliers)
                ->icon('heroicon-m-truck')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Active', $activeSuppliers)
                ->icon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Invited', $invitedSuppliers)
                ->icon('heroicon-m-envelope')
                ->color('warning'),
        ];
    }
}
