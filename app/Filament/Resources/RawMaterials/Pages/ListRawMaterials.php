<?php

namespace App\Filament\Resources\RawMaterials\Pages;

use App\Filament\Resources\RawMaterials\RawMaterialResource;
use App\Filament\Resources\RawMaterials\Widgets\RawMaterialsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRawMaterials extends ListRecords
{
    protected static string $resource = RawMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RawMaterialsOverview::class,
        ];
    }
}
