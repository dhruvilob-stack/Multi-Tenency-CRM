<?php

namespace App\Filament\Supplier\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $supplierId = auth()->user()?->supplier?->id;

                if (! $supplierId) {
                    return $query;
                }

                return $query->whereHas('purchaseOrder', function (Builder $poQuery) use ($supplierId): void {
                    $poQuery->where('supplier_id', $supplierId);
                });
            })
            ->columns([
                TextColumn::make('tracking_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchaseOrder.order_number')
                    ->label('PO')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('shipped_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('delivered_date')
                    ->date()
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
