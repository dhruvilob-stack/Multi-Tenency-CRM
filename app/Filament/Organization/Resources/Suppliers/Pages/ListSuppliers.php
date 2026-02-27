<?php

namespace App\Filament\Organization\Resources\Suppliers\Pages;

use App\Filament\Organization\Resources\Suppliers\SupplierResource;
use App\Filament\Organization\Resources\Suppliers\Widgets\SuppliersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SuppliersOverview::class,
        ];
    }
}
