<?php

namespace App\Filament\Supplier\Resources\Payments\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $supplierId = auth()->user()?->supplier?->id;

                if (! $supplierId) {
                    return $query;
                }

                return $query->whereHas('invoice.purchaseOrder', function (Builder $poQuery) use ($supplierId): void {
                    $poQuery->where('supplier_id', $supplierId);
                });
            })
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->sortable(),
                TextColumn::make('amount_paid_cents')
                    ->label('Amount')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                TextColumn::make('payment_mode')
                    ->toggleable(),
                BadgeColumn::make('status')
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
