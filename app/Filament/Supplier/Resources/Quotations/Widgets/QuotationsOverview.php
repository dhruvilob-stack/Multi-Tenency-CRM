<?php

namespace App\Filament\Supplier\Resources\Quotations\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Supplier\Resources\Quotations\QuotationResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuotationsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = QuotationResource::getEloquentQuery();

        $total = (clone $query)->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $accepted = (clone $query)->where('status', 'accepted')->count();

        return [
            Stat::make('Total quotations', $total)
                ->icon('heroicon-m-document-text')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Submitted', $submitted)
                ->icon('heroicon-m-paper-airplane')
                ->color('info')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'submitted'), days: 7)),

            Stat::make('Accepted', $accepted)
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'accepted'), days: 7)),
        ];
    }
}
