<?php

namespace App\Filament\Resources\RawMaterials\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Models\RawMaterial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RawMaterialsOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = RawMaterial::query();

        $total = (clone $query)->count();
        $active = (clone $query)->where('is_active', true)->count();
        $avgUnitCostCents = (int) round((clone $query)->avg('unit_cost_cents') ?? 0);

        return [
            Stat::make('Total materials', $total)
                ->icon('heroicon-m-beaker')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Active materials', $active)
                ->icon('heroicon-m-sparkles')
                ->color('success')
                ->chart($this->chartDailyCount((clone $query)->where('is_active', true), days: 7)),

            Stat::make('Avg unit cost', $this->formatUsdCents($avgUnitCostCents))
                ->icon('heroicon-m-banknotes')
                ->color('warning')
                ->chart($this->chartRecentInt((clone $query), 'unit_cost_cents', limit: 10)),
        ];
    }
}
