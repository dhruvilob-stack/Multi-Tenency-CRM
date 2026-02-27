<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Supplier\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Supplier\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use App\Filament\Supplier\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use App\Models\PurchaseOrder;
use App\Support\NotificationSectionManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'supplier_operations';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Supplier';

    protected static ?string $navigationParentItem = 'Operations';

    protected static ?string $slug = 'supplier/purchase-orders';

    protected static ?int $navigationSort = 2101;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'order_number',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['buyer']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $supplierId = auth()->user()?->supplier?->id;

        if (! $supplierId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('supplier_id', (int) $supplierId);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof PurchaseOrder) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'Buyer' => (string) ($record->buyer?->name ?? '—'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NotificationSectionManager::unreadCount('orders');

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return NotificationSectionManager::unreadCount('orders') > 0 ? 'danger' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrdersTable::configure($table);
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
            'index' => ListPurchaseOrders::route('/'),
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
