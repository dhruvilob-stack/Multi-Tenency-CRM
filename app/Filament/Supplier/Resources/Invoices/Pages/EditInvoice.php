<?php

namespace App\Filament\Supplier\Resources\Invoices\Pages;

use App\Filament\Supplier\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'status' => $data['status'] ?? $this->record->status,
            'compliance_status' => $data['compliance_status'] ?? $this->record->compliance_status,
        ];
    }
}
