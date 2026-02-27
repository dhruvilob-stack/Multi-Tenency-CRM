<?php

namespace App\Filament\Resources\ProductionOrders\Pages;

use App\Filament\Resources\ProductionOrders\ProductionOrderResource;
use App\Filament\Resources\ProductionOrders\Widgets\ProductionOrdersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductionOrders extends ListRecords
{
    protected static string $resource = ProductionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductionOrdersOverview::class,
        ];
    }
}
