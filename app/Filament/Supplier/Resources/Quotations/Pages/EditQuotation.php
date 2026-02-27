<?php

namespace App\Filament\Supplier\Resources\Quotations\Pages;

use App\Filament\Supplier\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'status' => $data['status'] ?? $this->record->status,
        ];
    }
}
