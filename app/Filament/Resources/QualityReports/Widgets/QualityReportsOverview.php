<?php

namespace App\Filament\Resources\QualityReports\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\QualityReport;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QualityReportsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = QualityReport::query();

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $failed = (clone $query)->where('status', 'failed')->count();

        return [
            Stat::make('Quality reports', $total)
                ->icon('heroicon-m-shield-check')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Pending', $pending)
                ->icon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Failed', $failed)
                ->icon('heroicon-m-x-circle')
                ->color($failed > 0 ? 'danger' : 'success'),
        ];
    }
}
