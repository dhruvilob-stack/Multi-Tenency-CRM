<?php

namespace App\Filament\Supplier\Resources\Invoices\Pages;

use App\Filament\Supplier\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
