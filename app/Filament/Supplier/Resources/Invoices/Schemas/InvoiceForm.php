<?php

namespace App\Filament\Supplier\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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
                    ->preload()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('external_reference')
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('amount_cents')
                    ->label('Amount (cents)')
                    ->numeric()
                    ->required()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('tax_cents')
                    ->label('Tax (cents)')
                    ->numeric()
                    ->default(0)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                DatePicker::make('due_date')
                    ->disabled(fn (?Model $record): bool => filled($record)),
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
