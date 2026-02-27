<?php

namespace App\Filament\Organization\Resources\Grns\Pages;

use App\Filament\Organization\Resources\Grns\GrnResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGrn extends EditRecord
{
    protected static string $resource = GrnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
