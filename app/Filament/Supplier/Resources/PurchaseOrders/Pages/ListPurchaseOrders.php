<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders\Pages;

use App\Filament\Concerns\MarksNotificationSectionRead;
use App\Filament\Supplier\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Supplier\Widgets\PurchaseOrdersOverview;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrders extends ListRecords
{
    use MarksNotificationSectionRead;

    protected static string $resource = PurchaseOrderResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->markNotificationSectionAsRead('orders');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PurchaseOrdersOverview::class,
        ];
    }
}
