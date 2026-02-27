<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use App\Support\NotificationSectionManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'products';

    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Catalog';

    protected static ?string $slug = 'manufacturer/products';

    protected static ?int $navigationSort = 201;

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
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
