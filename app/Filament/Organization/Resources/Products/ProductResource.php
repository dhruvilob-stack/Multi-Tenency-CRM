<?php

namespace App\Filament\Organization\Resources\Products;

use App\Filament\Organization\Resources\Products\Pages\ListProducts;
use App\Filament\Organization\Resources\Products\Schemas\ProductForm;
use App\Filament\Organization\Resources\Products\Tables\ProductsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\Product;
use App\Support\MasterCatalogSync;
use App\Support\NotificationSectionManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_catalog';

    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Catalog';

    protected static ?string $slug = 'organization/products';

    protected static ?int $navigationSort = 1003;

    public static function getModelLabel(): string
    {
        return __('resources.product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.products');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.products');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'sku',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Product) {
            return [];
        }

        return [
            'SKU' => (string) ($record->sku ?? '—'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NotificationSectionManager::unreadCount('catalog');

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return NotificationSectionManager::unreadCount('catalog') > 0 ? 'warning' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
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
