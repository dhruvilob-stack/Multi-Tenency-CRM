<?php

namespace App\Filament\Resources\DispatchOrders\Pages;

use App\Filament\Resources\DispatchOrders\DispatchOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDispatchOrder extends EditRecord
{
    protected static string $resource = DispatchOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
