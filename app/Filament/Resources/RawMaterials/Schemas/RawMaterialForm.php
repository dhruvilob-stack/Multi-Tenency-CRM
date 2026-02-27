<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class RawMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('organization_id')
                    ->relationship('organization', 'name', fn (Builder $query) => $query->where('type', 'manufacturer'))
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->maxLength(255),
                TextInput::make('unit')
                    ->maxLength(50),
                TextInput::make('unit_cost_cents')
                    ->label('Unit Cost (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
