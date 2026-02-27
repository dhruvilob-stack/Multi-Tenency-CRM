<?php

namespace App\Filament\Supplier\Resources\Invoices\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Supplier\Resources\Invoices\InvoiceResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = InvoiceResource::getEloquentQuery();

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $overdue = (clone $query)->where('status', 'overdue')->count();

        return [
            Stat::make('Total invoices', $total)
                ->icon('heroicon-m-receipt-refund')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Pending', $pending)
                ->icon('heroicon-m-clock')
                ->color('warning')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'pending'), days: 7)),

            Stat::make('Overdue', $overdue)
                ->icon('heroicon-m-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success')
                ->chart($this->chartDailyCount((clone $query)->where('status', 'overdue'), days: 7)),
        ];
    }
}
