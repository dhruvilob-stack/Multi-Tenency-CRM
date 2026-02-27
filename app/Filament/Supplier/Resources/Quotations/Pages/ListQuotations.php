<?php

namespace App\Filament\Supplier\Resources\Quotations\Pages;

use App\Filament\Supplier\Resources\Quotations\QuotationResource;
use App\Filament\Supplier\Resources\Quotations\Widgets\QuotationsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QuotationsOverview::class,
        ];
    }
}
