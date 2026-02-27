<?php

namespace App\Filament\Organization\Resources\Products\Pages;

use App\Filament\Concerns\MarksNotificationSectionRead;
use App\Filament\Organization\Resources\Products\ProductResource;
use App\Filament\Organization\Resources\Products\Widgets\ProductsOverview;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use MarksNotificationSectionRead;

    protected static string $resource = ProductResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->markNotificationSectionAsRead('catalog');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductsOverview::class,
        ];
    }
}
