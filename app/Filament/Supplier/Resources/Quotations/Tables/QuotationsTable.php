<?php

namespace App\Filament\Supplier\Resources\Quotations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $organizationId = auth()->user()?->organization_id;

                if (! $organizationId) {
                    return $query;
                }

                return $query->whereHas('rfq', function (Builder $rfqQuery) use ($organizationId): void {
                    $rfqQuery->where('supplier_id', $organizationId);
                });
            })
            ->columns([
                TextColumn::make('rfq.rfq_number')
                    ->label('RFQ')
                    ->sortable(),
                TextColumn::make('total_amount_cents')
                    ->label('Total')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
