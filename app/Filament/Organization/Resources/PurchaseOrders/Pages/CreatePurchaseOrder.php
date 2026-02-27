<?php

namespace App\Filament\Organization\Resources\PurchaseOrders\Pages;

use App\Filament\Organization\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;
}
