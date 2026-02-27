<?php

namespace App\Filament\Organization\Resources\Grns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GrnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'order_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('received_by')
                    ->relationship('receivedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('received_date'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                    ])
                    ->required(),
            ]);
    }
}
