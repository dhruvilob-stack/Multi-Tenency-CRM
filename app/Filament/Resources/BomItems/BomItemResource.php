<?php

namespace App\Filament\Resources\BomItems;

use App\Filament\Resources\BomItems\Pages\CreateBomItem;
use App\Filament\Resources\BomItems\Pages\EditBomItem;
use App\Filament\Resources\BomItems\Pages\ListBomItems;
use App\Filament\Resources\BomItems\Schemas\BomItemForm;
use App\Filament\Resources\BomItems\Tables\BomItemsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\BomItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BomItemResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'manufacturing';

    protected static ?string $model = BomItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Manufacturing';

    protected static ?string $slug = 'manufacturer/bom-items';

    protected static ?int $navigationSort = 102;

    public static function form(Schema $schema): Schema
    {
        return BomItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BomItemsTable::configure($table);
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
            'index' => ListBomItems::route('/'),
            'create' => CreateBomItem::route('/create'),
            'edit' => EditBomItem::route('/{record}/edit'),
        ];
    }
}
