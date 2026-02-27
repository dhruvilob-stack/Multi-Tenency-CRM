<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Concerns\MarksNotificationSectionRead;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Widgets\PurchaseOrdersOverview;
use Filament\Actions\CreateAction;
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
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PurchaseOrdersOverview::class,
        ];
    }
}
