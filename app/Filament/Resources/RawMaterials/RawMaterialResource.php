<?php

namespace App\Filament\Resources\RawMaterials;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\RawMaterials\Pages\CreateRawMaterial;
use App\Filament\Resources\RawMaterials\Pages\EditRawMaterial;
use App\Filament\Resources\RawMaterials\Pages\ListRawMaterials;
use App\Filament\Resources\RawMaterials\Schemas\RawMaterialForm;
use App\Filament\Resources\RawMaterials\Tables\RawMaterialsTable;
use App\Models\RawMaterial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RawMaterialResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'manufacturing';

    protected static ?string $model = RawMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-cube-transparent';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Manufacturing';

    protected static ?string $slug = 'manufacturer/raw-materials';

    protected static ?int $navigationSort = 101;

    public static function form(Schema $schema): Schema
    {
        return RawMaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RawMaterialsTable::configure($table);
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
            'index' => ListRawMaterials::route('/'),
            'create' => CreateRawMaterial::route('/create'),
            'edit' => EditRawMaterial::route('/{record}/edit'),
        ];
    }
}
