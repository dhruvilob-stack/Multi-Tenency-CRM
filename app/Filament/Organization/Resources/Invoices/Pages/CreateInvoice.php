<?php

namespace App\Filament\Organization\Resources\Invoices\Pages;

use App\Filament\Organization\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
