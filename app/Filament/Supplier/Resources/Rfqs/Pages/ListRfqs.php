<?php

namespace App\Filament\Supplier\Resources\Rfqs\Pages;

use App\Filament\Supplier\Resources\Rfqs\RfqResource;
use App\Filament\Supplier\Resources\Rfqs\Widgets\RfqsOverview;
use Filament\Resources\Pages\ListRecords;

class ListRfqs extends ListRecords
{
    protected static string $resource = RfqResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RfqsOverview::class,
        ];
    }
}
