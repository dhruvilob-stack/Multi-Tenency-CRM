<?php

namespace App\Filament\Organization\Resources\Categories\Pages;

use App\Filament\Organization\Resources\Categories\CategoryResource;
use App\Filament\Organization\Resources\Categories\Widgets\CategoriesOverview;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoriesOverview::class,
        ];
    }
}
