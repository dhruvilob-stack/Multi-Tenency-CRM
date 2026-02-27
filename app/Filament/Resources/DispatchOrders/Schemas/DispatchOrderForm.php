<?php

namespace App\Filament\Resources\DispatchOrders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DispatchOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'order_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('dispatch_number')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'dispatched' => 'Dispatched',
                        'delivered' => 'Delivered',
                    ])
                    ->required(),
                DateTimePicker::make('dispatched_at'),
            ]);
    }
}
