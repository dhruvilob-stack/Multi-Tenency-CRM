<?php

namespace App\Filament\Resources\QualityReports;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\QualityReports\Pages\CreateQualityReport;
use App\Filament\Resources\QualityReports\Pages\EditQualityReport;
use App\Filament\Resources\QualityReports\Pages\ListQualityReports;
use App\Filament\Resources\QualityReports\Schemas\QualityReportForm;
use App\Filament\Resources\QualityReports\Tables\QualityReportsTable;
use App\Models\QualityReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class QualityReportResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'quality_reports';

    protected static ?string $model = QualityReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Quality';

    protected static ?string $slug = 'manufacturer/quality-reports';

    protected static ?int $navigationSort = 501;

    public static function form(Schema $schema): Schema
    {
        return QualityReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualityReports::route('/'),
            'create' => CreateQualityReport::route('/create'),
            'edit' => EditQualityReport::route('/{record}/edit'),
        ];
    }
}
