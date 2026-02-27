<?php

namespace App\Filament\Organization\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                Select::make('status')
                    ->options([
                        'invited' => 'Invited',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('invited')
                    ->required(),
            ]);
    }
}
