<?php

namespace App\Filament\Organization\Resources\InventoryItems\Pages;

use App\Filament\Organization\Resources\InventoryItems\InventoryItemResource;
use App\Filament\Organization\Resources\InventoryItems\Widgets\InventoryOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryOverview::class,
        ];
    }
}
