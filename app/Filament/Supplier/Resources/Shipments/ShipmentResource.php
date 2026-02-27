<?php

namespace App\Filament\Supplier\Resources\Shipments;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Supplier\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Supplier\Resources\Shipments\Pages\EditShipment;
use App\Filament\Supplier\Resources\Shipments\Pages\ListShipments;
use App\Filament\Supplier\Resources\Shipments\Schemas\ShipmentForm;
use App\Filament\Supplier\Resources\Shipments\Tables\ShipmentsTable;
use App\Models\Shipment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShipmentResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'supplier_logistics';

    protected static ?string $model = Shipment::class;

    protected static ?string $recordTitleAttribute = 'tracking_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Supplier';

    protected static ?string $navigationParentItem = 'Logistics';

    protected static ?string $slug = 'supplier/shipments';

    protected static ?int $navigationSort = 2301;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'tracking_number',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['purchaseOrder']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $supplierId = auth()->user()?->supplier?->id;

        if (! $supplierId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('purchaseOrder', fn (Builder $purchaseOrderQuery) => $purchaseOrderQuery->where('supplier_id', (int) $supplierId));
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Shipment) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'PO' => (string) ($record->purchaseOrder?->order_number ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShipmentsTable::configure($table);
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
            'index' => ListShipments::route('/'),
            'create' => CreateShipment::route('/create'),
            'edit' => EditShipment::route('/{record}/edit'),
        ];
    }
}
