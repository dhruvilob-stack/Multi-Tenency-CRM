<?php

namespace App\Filament\Organization\Resources\Brands\Pages;

use App\Filament\Organization\Resources\Brands\BrandResource;
use App\Filament\Organization\Resources\Brands\Widgets\BrandsOverview;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BrandsOverview::class,
        ];
    }
}
