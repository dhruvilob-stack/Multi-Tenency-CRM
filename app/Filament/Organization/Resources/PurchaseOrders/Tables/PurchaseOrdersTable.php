<?php

namespace App\Filament\Organization\Resources\PurchaseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                $organizationId = auth()->user()?->organization_id;

                if (! $organizationId) {
                    return $query;
                }

                return $query->where('buyer_id', $organizationId);
            })
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
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
