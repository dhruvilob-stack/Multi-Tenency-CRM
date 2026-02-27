<?php

namespace App\Filament\Supplier\Resources\Rfqs\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RfqsTable
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
                TextColumn::make('rfq_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
