<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('tenant_code')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->options([
                        'manufacturer' => 'Manufacturer',
                        'supplier' => 'Supplier',
                        'buyer' => 'Buyer',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('assignedSuppliers', []);
                    }),
                Select::make('assignedSuppliers')
                    ->label('Assigned Suppliers')
                    ->relationship(
                        'assignedSuppliers',
                        'name',
                        fn (Builder $query) => $query->where('type', 'supplier')
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('type') === 'buyer'),
                TextInput::make('gst_number')
                    ->label('GST Number')
                    ->maxLength(100),
                TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->maxLength(255),
                Textarea::make('address')
                    ->rows(2)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required(),
                TextInput::make('currency_code')
                    ->label('Currency')
                    ->default('USD')
                    ->maxLength(3)
                    ->required(),
                TextInput::make('esg_score')
                    ->label('ESG Score')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100),
            ]);
    }
}
