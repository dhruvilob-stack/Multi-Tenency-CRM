<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('brand_id', null);
                        $set('category_id', null);
                    }),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->maxLength(255),
                Select::make('brand_id')
                    ->options(function (Get $get): array {
                        $organizationId = $get('organization_id');

                        if (! $organizationId) {
                            return [];
                        }

                        return Brand::query()
                            ->where('organization_id', $organizationId)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => blank($get('organization_id'))),
                Select::make('category_id')
                    ->options(function (Get $get): array {
                        $organizationId = $get('organization_id');

                        if (! $organizationId) {
                            return [];
                        }

                        return Category::query()
                            ->where('organization_id', $organizationId)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => blank($get('organization_id'))),
                TextInput::make('price_cents')
                    ->label('Price (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                TextInput::make('sustainability_score')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('carbon_kg_per_unit')
                    ->label('Carbon (kg/unit)')
                    ->numeric()
                    ->step(0.0001)
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('variant_options')
                    ->label('Variant Options (JSON)')
                    ->rows(3)
                    ->formatStateUsing(fn ($state): ?string => is_array($state) ? json_encode($state) : $state)
                    ->dehydrateStateUsing(function (?string $state): ?array {
                        if (blank($state)) {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    })
                    ->helperText('Example: {"size":["S","M","L"],"color":["Blue","Red"]}')
                    ->columnSpanFull(),
            ]);
    }
}
