<?php

namespace App\Filament\Organization\Resources\InventoryItems\Pages;

use App\Filament\Organization\Resources\InventoryItems\InventoryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;
}
