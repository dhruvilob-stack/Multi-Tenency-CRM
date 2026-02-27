<?php

namespace App\Filament\Resources\ProductionOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductionOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                TextColumn::make('quantity_planned')
                    ->sortable(),
                TextColumn::make('quantity_completed')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'submitted',
                        'primary' => 'approved',
                        'success' => 'in_production',
                        'success' => 'completed',
                    ])
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
