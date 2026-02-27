<?php

namespace App\Filament\Organization\Resources\Grns\Pages;

use App\Filament\Organization\Resources\Grns\GrnResource;
use App\Filament\Organization\Resources\Grns\Widgets\GrnsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGrns extends ListRecords
{
    protected static string $resource = GrnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GrnsOverview::class,
        ];
    }
}
