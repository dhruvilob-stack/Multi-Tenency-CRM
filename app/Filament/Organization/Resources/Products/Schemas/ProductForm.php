<?php

namespace App\Filament\Organization\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->maxLength(255),
                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('price_cents')
                    ->label('Price (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
