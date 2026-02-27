<?php

namespace App\Filament\Organization\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
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
                    ->maxLength(255)
                    ->disabled(fn (string $context): bool => $context === 'edit')
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                Select::make('role_id')
                    ->label('Role')
                    ->options(fn (): array => Role::query()
                        ->whereIn('name', ['organization_admin', 'supplier'])
                        ->pluck('name', 'id')
                        ->mapWithKeys(fn (string $name, int $id) => [$id => str($name)->replace('_', ' ')->title()->value()])
                        ->toArray())
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
