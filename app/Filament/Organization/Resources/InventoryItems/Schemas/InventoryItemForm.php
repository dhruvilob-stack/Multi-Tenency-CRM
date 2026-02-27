<?php

namespace App\Filament\Organization\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity_on_hand')
                    ->numeric()
                    ->required(),
                TextInput::make('reorder_threshold')
                    ->numeric()
                    ->required(),
            ]);
    }
}
