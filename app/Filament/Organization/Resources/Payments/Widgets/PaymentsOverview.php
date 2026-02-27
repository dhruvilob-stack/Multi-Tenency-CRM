<?php

namespace App\Filament\Organization\Resources\Payments\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Organization\Resources\Payments\PaymentResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = PaymentResource::getEloquentQuery();

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $completed = (clone $query)->where('status', 'completed')->count();

        return [
            Stat::make('Payments', $total)
                ->icon('heroicon-m-banknotes')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Pending', $pending)
                ->icon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed', $completed)
                ->icon('heroicon-m-sparkles')
                ->color('success'),
        ];
    }
}
