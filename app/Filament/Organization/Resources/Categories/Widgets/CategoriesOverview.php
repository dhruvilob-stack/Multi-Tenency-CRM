<?php

namespace App\Filament\Organization\Resources\Categories\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoriesOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $organizationId = auth()->user()?->organization_id;

        $query = Category::query()->when(
            $organizationId,
            fn ($query, int $organizationId) => $query->where('organization_id', $organizationId),
            fn ($query) => $query->whereRaw('1 = 0'),
        );

        $total = (clone $query)->count();
        $newThisWeek = (clone $query)->where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Categories', $total)
                ->icon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('New (7 days)', $newThisWeek)
                ->icon('heroicon-m-sun')
                ->color('info'),
        ];
    }
}
