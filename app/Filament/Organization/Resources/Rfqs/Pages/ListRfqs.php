<?php

namespace App\Filament\Organization\Resources\Rfqs\Pages;

use App\Filament\Organization\Resources\Rfqs\RfqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRfqs extends ListRecords
{
    protected static string $resource = RfqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New RFQ'),
        ];
    }
}
