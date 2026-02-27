<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('buyer_id')
                    ->relationship('buyer', 'name', fn (Builder $query) => $query->where('type', 'buyer'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->default(auth()->user()?->supplier?->id)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'in_production' => 'In Production',
                        'dispatched' => 'Dispatched',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                    ])
                    ->required(),
                TextInput::make('total_amount_cents')
                    ->label('Total Amount (cents)')
                    ->numeric()
                    ->required()
                    ->disabled(fn (?Model $record): bool => filled($record)),
            ]);
    }
}
