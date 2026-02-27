<?php

namespace App\Filament\Organization\Resources\Users;

use App\Filament\Organization\Resources\Users\Pages\CreateUser;
use App\Filament\Organization\Resources\Users\Pages\EditUser;
use App\Filament\Organization\Resources\Users\Pages\ListUsers;
use App\Filament\Organization\Resources\Users\Schemas\UserForm;
use App\Filament\Organization\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_access';

    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Access';

    protected static ?string $slug = 'organization/users';

    protected static ?int $navigationSort = 1501;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
            'phone',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('organization_id', (int) $organizationId);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof User) {
            return [];
        }

        return [
            'Email' => (string) $record->email,
            'Role' => (string) ($record->role?->name ?? $record->role ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
