<?php

namespace App\Filament\Organization\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                $organizationId = auth()->user()?->organization_id;

                if (! $organizationId) {
                    return $query;
                }

                return $query->whereHas('invoice.purchaseOrder', function (Builder $poQuery) use ($organizationId): void {
                    $poQuery->where('buyer_id', $organizationId);
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
