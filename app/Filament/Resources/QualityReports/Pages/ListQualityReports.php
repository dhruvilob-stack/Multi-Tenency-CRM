<?php

namespace App\Filament\Resources\QualityReports\Pages;

use App\Filament\Resources\QualityReports\QualityReportResource;
use App\Filament\Resources\QualityReports\Widgets\QualityReportsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualityReports extends ListRecords
{
    protected static string $resource = QualityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QualityReportsOverview::class,
        ];
    }
}
