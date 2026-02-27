<?php

namespace App\Filament\Resources\QualityReports\Pages;

use App\Filament\Resources\QualityReports\QualityReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQualityReport extends EditRecord
{
    protected static string $resource = QualityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
