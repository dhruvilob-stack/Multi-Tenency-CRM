<?php

namespace App\Filament\Organization\Resources\Quotations\Pages;

use App\Filament\Organization\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\ListRecords;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
