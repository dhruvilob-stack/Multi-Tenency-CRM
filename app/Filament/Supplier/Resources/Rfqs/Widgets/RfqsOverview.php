<?php

namespace App\Filament\Supplier\Resources\Rfqs\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Supplier\Resources\Rfqs\RfqResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RfqsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = RfqResource::getEloquentQuery();

        $total = (clone $query)->count();
        $open = (clone $query)->where('status', 'open')->count();

        $dueSoon = (clone $query)
            ->where('status', 'open')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', Carbon::now()->toDateString())
            ->whereDate('due_date', '<=', Carbon::now()->addDays(7)->toDateString())
            ->count();

        return [
            Stat::make('Total RFQs', $total)
                ->icon('heroicon-m-inbox')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Open', $open)
                ->icon('heroicon-m-sparkles')
                ->color('info')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'open'), days: 7)),

            Stat::make('Due soon', $dueSoon)
                ->icon('heroicon-m-exclamation-triangle')
                ->color($dueSoon > 0 ? 'warning' : 'success'),
        ];
    }
}
