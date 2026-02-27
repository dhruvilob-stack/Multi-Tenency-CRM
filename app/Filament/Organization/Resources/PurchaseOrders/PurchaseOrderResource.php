<?php

namespace App\Filament\Organization\Resources\PurchaseOrders;

use App\Filament\Organization\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Organization\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Organization\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Organization\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use App\Filament\Organization\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
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

    protected static string $crmModule = 'org_operations';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Operations';

    protected static ?string $slug = 'organization/purchase-orders';

    protected static ?int $navigationSort = 1102;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'order_number',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['buyer', 'supplier']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('buyer_id', (int) $organizationId);
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
