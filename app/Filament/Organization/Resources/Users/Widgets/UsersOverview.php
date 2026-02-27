<?php

namespace App\Filament\Organization\Resources\Users\Widgets;

use App\Filament\Concerns\BuildsTrendCharts;
use App\Filament\Organization\Resources\Users\UserResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersOverview extends StatsOverviewWidget
{
    use BuildsTrendCharts;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $query = UserResource::getEloquentQuery();

        $totalUsers = (clone $query)->count();
        $activeUsers = (clone $query)->where('is_active', true)->count();
        $admins = (clone $query)->where('role', 'organization_admin')->count();

        return [
            Stat::make('Users', $totalUsers)
                ->icon('heroicon-m-users')
                ->color('primary')
                ->chart($this->chartDailyCount((clone $query), days: 7)),

            Stat::make('Active', $activeUsers)
                ->icon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Admins', $admins)
                ->icon('heroicon-m-shield-check')
                ->color('info'),
        ];
    }
}
