<?php

namespace App\Filament\Resources\Rfqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RfqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('buyer_id')
                    ->relationship('buyer', 'name')
                    ->disabled(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->disabled(),
                TextInput::make('rfq_number')
                    ->disabled(),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'submitted' => 'Submitted',
                        'closed' => 'Closed',
                    ])
                    ->required(),
                TextInput::make('due_date')
                    ->type('date')
                    ->disabled(),
            ]);
    }
}
