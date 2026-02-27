<?php

namespace App\Filament\Organization\Resources\Grns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GrnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $organizationId = auth()->user()?->organization_id;

                if (! $organizationId) {
                    return $query;
                }

                return $query->whereHas('purchaseOrder', function (Builder $poQuery) use ($organizationId): void {
                    $poQuery->where('buyer_id', $organizationId);
                });
            })
            ->columns([
                TextColumn::make('purchaseOrder.order_number')
                    ->label('PO')
                    ->sortable(),
                TextColumn::make('received_by')
                    ->label('Received By')
                    ->sortable(),
                TextColumn::make('received_date')
                    ->date()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
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
