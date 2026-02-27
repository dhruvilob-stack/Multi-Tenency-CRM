<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Support\CrmAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'users';

    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Master CRM Panel';

    protected static ?string $navigationLabel = 'Authorisation Management';

    protected static ?string $slug = 'master/authorisation-management';

    protected static ?int $navigationSort = 1;

    /**
     * @return array<int, string>
     */
    public static function roleNames(): array
    {
        return [
            'super_admin',
            'manufacturer',
            'organization_admin',
            'supplier',
        ];
    }

    public static function ensureCoreRoles(): void
    {
        foreach (static::roleNames() as $roleName) {
            Role::query()->firstOrCreate(
                ['name' => $roleName],
                ['permissions' => static::defaultPermissionsForRole($roleName)]
            );
        }
    }

    /**
     * @return array<int, string>
     */
    public static function roleOptions(): array
    {
        static::ensureCoreRoles();

        return Role::query()
            ->whereIn('name', static::roleNames())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->mapWithKeys(fn (string $name, int $id) => [$id => str($name)->replace('_', ' ')->title()->value()])
            ->toArray();
    }

    /**
     * @return array<int, string>
     */
    public static function defaultPermissionsForRole(string $role): array
    {
        return CrmAccess::permissionsForRole($role);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['organization', 'role']);

        if (! auth()->user()?->isSuperAdmin()) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data): array
    {
        $roleName = Role::query()->whereKey($data['role_id'] ?? null)->value('name');
        $roleName = is_string($roleName) ? $roleName : (string) ($data['role'] ?? 'organization_admin');

        $data['role'] = $roleName;

        if ($roleName === 'super_admin') {
            $data['organization_id'] = null;
        }

        if (! isset($data['permissions']) || ! is_array($data['permissions'])) {
            $data['permissions'] = static::defaultPermissionsForRole($roleName);
        }

        return $data;
    }

    public static function synchronizeSupplierLink(User $user): void
    {
        if ($user->role !== 'supplier') {
            if ($user->supplier) {
                $user->supplier->forceFill([
                    'status' => 'inactive',
                ])->save();
            }

            return;
        }

        if (! filled($user->organization_id)) {
            return;
        }

        Supplier::query()->updateOrCreate(
            [
                'organization_id' => (int) $user->organization_id,
                'email' => $user->email,
            ],
            [
                'name' => $user->name,
                'user_id' => $user->id,
                'status' => $user->is_active ? 'active' : 'inactive',
            ]
        );
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
        return [];
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
