<?php

namespace App\Filament\Supplier\Resources\Shipments\Pages;

use App\Filament\Supplier\Resources\Shipments\ShipmentResource;
use Filament\Resources\Pages\EditRecord;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'status' => $data['status'] ?? $this->record->status,
            'delivered_date' => ($data['status'] ?? $this->record->status) === 'delivered'
                ? ($this->record->delivered_date ?? now()->toDateString())
                : $this->record->delivered_date,
        ];
    }
}
