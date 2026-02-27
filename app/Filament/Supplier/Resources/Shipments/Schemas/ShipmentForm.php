<?php

namespace App\Filament\Supplier\Resources\Shipments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'order_number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('tracking_number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('status')
                    ->options([
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                        'delayed' => 'Delayed',
                    ])
                    ->required(),
                DatePicker::make('shipped_date')
                    ->disabled(fn (?Model $record): bool => filled($record)),
                DatePicker::make('delivered_date')
                    ->disabled(fn (?Model $record): bool => filled($record)),
            ]);
    }
}
