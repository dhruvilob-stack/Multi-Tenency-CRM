<?php

namespace App\Filament\Supplier\Resources\Shipments\Pages;

use App\Filament\Supplier\Resources\Shipments\ShipmentResource;
use App\Filament\Supplier\Resources\Shipments\Widgets\ShipmentsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ShipmentsOverview::class,
        ];
    }
}
