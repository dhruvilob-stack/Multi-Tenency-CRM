<?php

namespace App\Filament\Organization\Resources\Brands;

use App\Filament\Organization\Resources\Brands\Pages\ListBrands;
use App\Filament\Organization\Resources\Brands\Schemas\BrandForm;
use App\Filament\Organization\Resources\Brands\Tables\BrandsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\Brand;
use App\Support\MasterCatalogSync;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BrandResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_catalog';

    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Catalog';

    protected static ?string $slug = 'organization/brands';

    protected static ?int $navigationSort = 1001;

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
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

        app(MasterCatalogSync::class)->ensureSyncedForPartnerOrganization((int) $organizationId);

        return $query->where('organization_id', (int) $organizationId);
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
            'index' => ListBrands::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
