<?php

namespace App\Filament\Resources\DispatchOrders\Pages;

use App\Filament\Resources\DispatchOrders\DispatchOrderResource;
use App\Filament\Resources\DispatchOrders\Widgets\DispatchOrdersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispatchOrders extends ListRecords
{
    protected static string $resource = DispatchOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DispatchOrdersOverview::class,
        ];
    }
}
