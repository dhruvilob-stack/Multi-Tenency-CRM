<?php

namespace App\Filament\Resources\Organizations\Tables;

use App\Filament\Tables\Columns\RevenueSparklineColumn;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\OrganizationRevenueSnapshot;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->with(['latestRevenueSnapshot', 'recentRevenueSnapshots']);
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray' => 'inactive',
                    ])
                    ->sortable(),
                TextColumn::make('currency_code')
                    ->label('Currency')
                    ->sortable(),
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('recordRevenue')
                    ->label('Record Revenue')
                    ->icon('heroicon-m-chart-bar-square')
                    ->form([
                        TextInput::make('amount')
                            ->label('Amount (USD)')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->action(function (Organization $record, array $data): void {
                        $amount = (float) $data['amount'];

                        $snapshot = OrganizationRevenueSnapshot::query()->create([
                            'organization_id' => $record->id,
                            'revenue_cents' => (int) round($amount * 100),
                            'recorded_at' => now(),
                        ]);

                        AuditLog::query()->create([
                            'organization_id' => $record->id,
                            'user_id' => auth()->id(),
                            'action' => 'organization.revenue.recorded',
                            'auditable_type' => OrganizationRevenueSnapshot::class,
                            'auditable_id' => $snapshot->id,
                            'metadata' => ['amount' => $amount],
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'created_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Revenue snapshot recorded.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
