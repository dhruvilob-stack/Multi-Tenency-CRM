<?php

namespace App\Filament\Supplier\Widgets;

use App\Models\PurchaseOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PurchaseOrdersOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    public function getHeading(): ?string
    {
        return __('dashboard.orders_overview');
    }

    protected function getStats(): array
    {
        $supplierId = auth()->user()?->supplier?->id;

        $query = PurchaseOrder::query()->when(
            $supplierId,
            fn (Builder $query, int $supplierId): Builder => $query->where('supplier_id', $supplierId),
            fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
        );

        return $this->buildStats($query);
    }

    /**
     * @return array<int, Stat>
     */
    protected function buildStats(Builder $query): array
    {
        $totalOrders = (clone $query)->count();
        $activeOrders = (clone $query)->where('status', '!=', 'completed')->count();
        $pendingOrders = (clone $query)->whereIn('status', ['draft', 'submitted'])->count();

        return [
            Stat::make(__('dashboard.total_orders'), $totalOrders)
                ->icon('heroicon-m-clipboard-document-check')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make(__('dashboard.active_orders'), $activeOrders)
                ->icon('heroicon-m-bolt')
                ->color('success')
                ->description(__('dashboard.active_orders_help'))
                ->chart($this->chartDailyCount((clone $query)->where('status', '!=', 'completed'), days: 7)),

            Stat::make(__('dashboard.pending_orders'), $pendingOrders)
                ->icon('heroicon-m-clock')
                ->color('warning')
                ->description(__('dashboard.pending_orders_help'))
                ->chart($this->chartDailyCount((clone $query)->whereIn('status', ['draft', 'submitted']), days: 7)),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function chartDailyCount(Builder $query, int $days): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        $countsByDate = (clone $query)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->all();

        $chart = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chart[] = (int) ($countsByDate[$date] ?? 0);
        }

        return $chart;
    }
}
