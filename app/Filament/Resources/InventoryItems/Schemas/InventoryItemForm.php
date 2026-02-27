<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InventoryItemForm
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
                        $set('product_id', null);
                    }),
                Select::make('product_id')
                    ->options(function (Get $get): array {
                        $organizationId = $get('organization_id');

                        if (! $organizationId) {
                            return [];
                        }

                        return Product::query()
                            ->where('organization_id', $organizationId)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => blank($get('organization_id'))),
                TextInput::make('quantity_on_hand')
                    ->numeric()
                    ->required(),
                TextInput::make('reorder_threshold')
                    ->numeric()
                    ->required(),
                Toggle::make('expiry_tracking_enabled')
                    ->label('Enable Expiry Tracking')
                    ->default(false)
                    ->live(),
                DatePicker::make('next_expiry_date')
                    ->visible(fn (Get $get): bool => (bool) $get('expiry_tracking_enabled')),
            ]);
    }
}
