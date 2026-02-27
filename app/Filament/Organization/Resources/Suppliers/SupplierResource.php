<?php

namespace App\Filament\Organization\Resources\Suppliers;

use App\Filament\Organization\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Organization\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Organization\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\Organization\Resources\Suppliers\Tables\SuppliersTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupplierResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_partners';

    protected static ?string $model = Supplier::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Partners';

    protected static ?string $slug = 'organization/suppliers';

    protected static ?int $navigationSort = 1401;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
            'status',
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
        if (! $record instanceof Supplier) {
            return [];
        }

        return [
            'Email' => (string) ($record->email ?? '—'),
            'Status' => (string) $record->status,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
