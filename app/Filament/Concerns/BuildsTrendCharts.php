<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BuildsTrendCharts
{
    /**
     * @return array<int, int>
     */
    protected function chartDailyCount(Builder $query, int $days = 7, string $dateColumn = 'created_at'): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        $countsByDate = (clone $query)
            ->where($dateColumn, '>=', $since)
            ->selectRaw("DATE({$dateColumn}) as date, COUNT(*) as total")
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
    protected function chartRecentInt(Builder $query, string $column, int $limit = 10): array
    {
        return (clone $query)
            ->whereNotNull($column)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->pluck($column)
            ->reverse()
            ->values()
            ->map(fn ($value): int => (int) $value)
            ->all();
    }

    protected function formatUsdCents(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
