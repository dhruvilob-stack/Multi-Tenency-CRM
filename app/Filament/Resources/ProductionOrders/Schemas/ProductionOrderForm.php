<?php

namespace App\Filament\Resources\ProductionOrders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductionOrderForm
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
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity_planned')
                    ->numeric()
                    ->required(),
                TextInput::make('quantity_completed')
                    ->numeric()
                    ->default(0),
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
                TextInput::make('machine_utilization_percent')
                    ->label('Machine Utilization %')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('defect_ratio_percent')
                    ->label('Defect Ratio %')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('cost_per_unit_cents')
                    ->label('Cost / Unit (cents)')
                    ->numeric()
                    ->minValue(0),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
