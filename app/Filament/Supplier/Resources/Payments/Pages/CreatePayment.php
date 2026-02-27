<?php

namespace App\Filament\Supplier\Resources\Payments\Pages;

use App\Filament\Supplier\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
