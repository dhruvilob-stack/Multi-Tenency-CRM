<?php

namespace App\Filament\Resources\PurchaseOrders;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use App\Filament\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
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

    protected static string $crmModule = 'purchase_orders';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Procurement';

    protected static ?string $slug = 'manufacturer/purchase-orders';

    protected static ?int $navigationSort = 801;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'order_number',
            'status',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['buyer', 'supplier']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof PurchaseOrder) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'Buyer' => (string) ($record->buyer?->name ?? '—'),
            'Supplier' => (string) ($record->supplier?->name ?? '—'),
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
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
