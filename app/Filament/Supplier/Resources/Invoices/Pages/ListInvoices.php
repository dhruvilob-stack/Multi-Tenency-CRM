<?php

namespace App\Filament\Supplier\Resources\Invoices\Pages;

use App\Filament\Supplier\Resources\Invoices\InvoiceResource;
use App\Filament\Supplier\Resources\Invoices\Widgets\InvoicesOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InvoicesOverview::class,
        ];
    }
}
