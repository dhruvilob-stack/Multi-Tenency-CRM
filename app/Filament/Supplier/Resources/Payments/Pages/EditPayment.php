<?php

namespace App\Filament\Supplier\Resources\Payments\Pages;

use App\Filament\Supplier\Resources\Payments\PaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
