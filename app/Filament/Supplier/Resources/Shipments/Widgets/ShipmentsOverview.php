<?php

namespace App\Filament\Supplier\Resources\Shipments\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Supplier\Resources\Shipments\ShipmentResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShipmentsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = ShipmentResource::getEloquentQuery();

        $total = (clone $query)->count();
        $inTransit = (clone $query)->where('status', 'in_transit')->count();
        $delayed = (clone $query)->where('status', 'delayed')->count();

        return [
            Stat::make('Total shipments', $total)
                ->icon('heroicon-m-truck')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('In transit', $inTransit)
                ->icon('heroicon-m-arrow-path-rounded-square')
                ->color('info')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'in_transit'), days: 7)),

            Stat::make('Delayed', $delayed)
                ->icon('heroicon-m-fire')
                ->color($delayed > 0 ? 'danger' : 'success')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'delayed'), days: 7)),
        ];
    }
}
