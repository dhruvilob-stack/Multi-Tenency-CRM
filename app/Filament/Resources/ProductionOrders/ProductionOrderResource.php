<?php

namespace App\Filament\Resources\ProductionOrders;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\ProductionOrders\Pages\CreateProductionOrder;
use App\Filament\Resources\ProductionOrders\Pages\EditProductionOrder;
use App\Filament\Resources\ProductionOrders\Pages\ListProductionOrders;
use App\Filament\Resources\ProductionOrders\Schemas\ProductionOrderForm;
use App\Filament\Resources\ProductionOrders\Tables\ProductionOrdersTable;
use App\Models\ProductionOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductionOrderResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'manufacturing';

    protected static ?string $model = ProductionOrder::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Manufacturing';

    protected static ?string $slug = 'manufacturer/production-orders';

    protected static ?int $navigationSort = 103;

    public static function form(Schema $schema): Schema
    {
        return ProductionOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionOrdersTable::configure($table);
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
            'index' => ListProductionOrders::route('/'),
            'create' => CreateProductionOrder::route('/create'),
            'edit' => EditProductionOrder::route('/{record}/edit'),
        ];
    }
}
