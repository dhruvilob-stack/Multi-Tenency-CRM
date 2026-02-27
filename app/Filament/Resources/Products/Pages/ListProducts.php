<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Concerns\MarksNotificationSectionRead;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Widgets\ProductsOverview;
use Filament\Actions\CreateAction;
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
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductsOverview::class,
        ];
    }
}
