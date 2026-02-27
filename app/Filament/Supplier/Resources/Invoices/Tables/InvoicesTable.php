<?php

namespace App\Filament\Supplier\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
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
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchaseOrder.order_number')
                    ->label('PO')
                    ->sortable(),
                TextColumn::make('amount_cents')
                    ->label('Amount')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                TextColumn::make('tax_cents')
                    ->label('Tax')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('due_date')
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
