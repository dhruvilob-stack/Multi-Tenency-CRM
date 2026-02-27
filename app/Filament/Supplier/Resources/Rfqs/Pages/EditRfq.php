<?php

namespace App\Filament\Supplier\Resources\Rfqs\Pages;

use App\Filament\Supplier\Resources\Rfqs\RfqResource;
use Filament\Resources\Pages\EditRecord;

class EditRfq extends EditRecord
{
    protected static string $resource = RfqResource::class;

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
