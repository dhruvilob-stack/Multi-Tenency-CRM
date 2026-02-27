<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $supplierId = auth()->user()?->supplier?->id;

                if (! $supplierId) {
                    return $query;
                }

                return $query->where('supplier_id', $supplierId);
            })
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('total_amount_cents')
                    ->label('Total')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
