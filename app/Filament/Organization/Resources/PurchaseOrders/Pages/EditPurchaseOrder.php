<?php

namespace App\Filament\Organization\Resources\PurchaseOrders\Pages;

use App\Filament\Organization\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

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
