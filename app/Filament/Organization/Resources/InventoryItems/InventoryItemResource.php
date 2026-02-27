<?php

namespace App\Filament\Organization\Resources\InventoryItems;

use App\Filament\Organization\Resources\InventoryItems\Pages\CreateInventoryItem;
use App\Filament\Organization\Resources\InventoryItems\Pages\EditInventoryItem;
use App\Filament\Organization\Resources\InventoryItems\Pages\ListInventoryItems;
use App\Filament\Organization\Resources\InventoryItems\Schemas\InventoryItemForm;
use App\Filament\Organization\Resources\InventoryItems\Tables\InventoryItemsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\InventoryItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InventoryItemResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_warehouse';

    protected static ?string $model = InventoryItem::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Warehouse';

    protected static ?string $slug = 'organization/inventory-items';

    protected static ?int $navigationSort = 1201;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery()->with(['product']);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        if (! $record instanceof InventoryItem) {
            return parent::getGlobalSearchResultTitle($record);
        }

        $productName = $record->product?->name;

        return filled($productName)
            ? "{$productName} (Inv #{$record->getKey()})"
            : "Inventory Item #{$record->getKey()}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof InventoryItem) {
            return [];
        }

        return [
            'On hand' => (string) $record->quantity_on_hand,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InventoryItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryItemsTable::configure($table);
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryItems::route('/'),
            'create' => CreateInventoryItem::route('/create'),
            'edit' => EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
