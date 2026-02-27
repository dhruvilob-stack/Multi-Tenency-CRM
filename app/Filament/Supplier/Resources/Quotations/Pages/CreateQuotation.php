<?php

namespace App\Filament\Supplier\Resources\Quotations\Pages;

use App\Filament\Supplier\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;
}
