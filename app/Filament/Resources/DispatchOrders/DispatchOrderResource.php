<?php

namespace App\Filament\Resources\DispatchOrders;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\DispatchOrders\Pages\CreateDispatchOrder;
use App\Filament\Resources\DispatchOrders\Pages\EditDispatchOrder;
use App\Filament\Resources\DispatchOrders\Pages\ListDispatchOrders;
use App\Filament\Resources\DispatchOrders\Schemas\DispatchOrderForm;
use App\Filament\Resources\DispatchOrders\Tables\DispatchOrdersTable;
use App\Models\DispatchOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DispatchOrderResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'logistics';

    protected static ?string $model = DispatchOrder::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Logistics';

    protected static ?string $slug = 'manufacturer/dispatch-orders';

    protected static ?int $navigationSort = 601;

    public static function form(Schema $schema): Schema
    {
        return DispatchOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DispatchOrdersTable::configure($table);
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
            'index' => ListDispatchOrders::route('/'),
            'create' => CreateDispatchOrder::route('/create'),
            'edit' => EditDispatchOrder::route('/{record}/edit'),
        ];
    }
}
