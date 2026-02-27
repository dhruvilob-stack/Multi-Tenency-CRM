<?php

namespace App\Filament\Widgets;

use App\Filament\Tables\Columns\RevenueSparklineColumn;
use App\Models\Organization;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OrganizationsRevenueTable extends TableWidget
{
    protected static ?string $heading = 'Live Revenue by Organization';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Organization::query()->with(['latestRevenueSnapshot', 'recentRevenueSnapshots']))
            ->poll('10s')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('latestRevenueSnapshot.revenue_cents')
                    ->label('Revenue')
                    ->money(fn (Organization $record): string => $record->currency_code ?: 'USD', divideBy: 100)
                    ->sortable(),
                RevenueSparklineColumn::make('revenue_trend')
                    ->label('Flow')
                    ->data(fn (Organization $record): array => $record->recentRevenueSnapshots
                        ->sortBy('recorded_at')
                        ->pluck('revenue_cents')
                        ->map(fn (int $value): float => $value / 100)
                        ->values()
                        ->all()),
            ])
            ->filters([]);
    }
}
