<?php

namespace App\Filament\Organization\Resources\Payments\Pages;

use App\Filament\Organization\Resources\Payments\PaymentResource;
use App\Filament\Organization\Resources\Payments\Widgets\PaymentsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentsOverview::class,
        ];
    }
}
