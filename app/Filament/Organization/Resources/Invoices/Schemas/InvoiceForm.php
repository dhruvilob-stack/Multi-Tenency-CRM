<?php

namespace App\Filament\Organization\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
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
                TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('external_reference')
                    ->maxLength(255),
                TextInput::make('amount_cents')
                    ->label('Amount (cents)')
                    ->numeric()
                    ->required(),
                TextInput::make('tax_cents')
                    ->label('Tax (cents)')
                    ->numeric()
                    ->default(0),
                DatePicker::make('due_date'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ])
                    ->required(),
                Select::make('compliance_status')
                    ->options([
                        'pending_review' => 'Pending Review',
                        'compliant' => 'Compliant',
                        'flagged' => 'Flagged',
                    ]),
            ]);
    }
}
