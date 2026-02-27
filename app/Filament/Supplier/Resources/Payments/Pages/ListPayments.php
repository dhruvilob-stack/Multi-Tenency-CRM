<?php

namespace App\Filament\Supplier\Resources\Payments\Pages;

use App\Filament\Supplier\Resources\Payments\PaymentResource;
use App\Filament\Supplier\Resources\Payments\Widgets\PaymentsOverview;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentsOverview::class,
        ];
    }
}
