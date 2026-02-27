<?php

namespace App\Filament\Resources\DispatchOrders\Pages;

use App\Filament\Resources\DispatchOrders\DispatchOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDispatchOrder extends CreateRecord
{
    protected static string $resource = DispatchOrderResource::class;
}
