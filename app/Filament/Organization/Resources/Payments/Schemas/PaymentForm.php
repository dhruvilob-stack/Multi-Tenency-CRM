<?php

namespace App\Filament\Organization\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->relationship('invoice', 'invoice_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('amount_paid_cents')
                    ->label('Amount Paid (cents)')
                    ->numeric()
                    ->required(),
                TextInput::make('payment_mode')
                    ->maxLength(50),
                TextInput::make('transaction_reference')
                    ->maxLength(255),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
            ]);
    }
}
