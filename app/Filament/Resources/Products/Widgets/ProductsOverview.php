<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ProductsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $productsQuery = Product::query();

        $totalProducts = (clone $productsQuery)->count();
        $activeProducts = (clone $productsQuery)->where('is_active', true)->count();

        $avgPriceCents = (int) round((clone $productsQuery)->avg('price_cents') ?? 0);
        $avgPriceUsd = '$'.number_format($avgPriceCents / 100, 2);

        $avgSustainability = (float) ((clone $productsQuery)->avg('sustainability_score') ?? 0);

        return [
            Stat::make(__('dashboard.total_products'), $totalProducts)
                ->icon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $productsQuery), days: 7)),

            Stat::make(__('dashboard.active_products'), $activeProducts)
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->description($totalProducts > 0 ? round(($activeProducts / $totalProducts) * 100).'% active' : '0% active')
                ->chart($this->chartDailyCount((clone $productsQuery)->where('is_active', true), days: 7)),

            Stat::make(__('dashboard.avg_price'), $avgPriceUsd)
                ->icon('heroicon-m-currency-dollar')
                ->color('warning')
                ->description('Across all products')
                ->chart($this->chartRecentPrices((clone $productsQuery), limit: 10)),

            Stat::make(__('dashboard.avg_sustainability'), number_format($avgSustainability, 2))
                ->icon('heroicon-m-globe-alt')
                ->color('info')
                ->description('Sustainability score'),
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

    /**
     * @return array<int, int>
     */
    private function chartRecentPrices(Builder $query, int $limit): array
    {
        return (clone $query)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->pluck('price_cents')
            ->reverse()
            ->values()
            ->map(fn ($value): int => (int) $value)
            ->all();
    }
}
