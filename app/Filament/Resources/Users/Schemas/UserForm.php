<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\UserResource;
use App\Models\Organization;
use App\Models\Role;
use App\Support\CrmAccess;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                Select::make('role_id')
                    ->label('Role')
                    ->options(fn (): array => UserResource::roleOptions())
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (mixed $state, callable $set): void {
                        if (! filled($state)) {
                            return;
                        }

                        $roleName = Role::query()->whereKey($state)->value('name');

                        if (! is_string($roleName)) {
                            return;
                        }

                        $set('permissions', CrmAccess::permissionsForRole($roleName));
                    }),
                Select::make('organization_id')
                    ->label('Organization')
                    ->options(fn (): array => Organization::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload(),
                CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->options(CrmAccess::permissionOptions())
                    ->columns(2)
                    ->bulkToggleable()
                    ->helperText('These permissions are applied to this user account.')
                    ->default([]),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
