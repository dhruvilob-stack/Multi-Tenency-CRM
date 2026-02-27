<?php

namespace App\Filament\Resources\QualityReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QualityReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('production_order_id')
                    ->relationship('productionOrder', 'order_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'passed' => 'Passed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
                DatePicker::make('inspection_date'),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
