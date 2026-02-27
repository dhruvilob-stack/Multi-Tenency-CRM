<?php

namespace App\Filament\Organization\Resources\Invoices\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Organization\Resources\Invoices\InvoiceResource;
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
        $open = (clone $query)->whereIn('status', ['pending', 'overdue'])->count();
        $paid = (clone $query)->where('status', 'paid')->count();

        return [
            Stat::make('Invoices', $total)
                ->icon('heroicon-m-receipt-refund')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Open', $open)
                ->icon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Paid', $paid)
                ->icon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
