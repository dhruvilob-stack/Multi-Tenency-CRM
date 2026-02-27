<?php

namespace App\Filament\Resources\BomItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BomItemForm
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
                Select::make('raw_material_id')
                    ->relationship('rawMaterial', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('alternative_raw_material_id')
                    ->relationship('alternativeRawMaterial', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity_required')
                    ->numeric()
                    ->required(),
                TextInput::make('unit_cost_cents')
                    ->label('Unit Cost (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }
}
