<?php

namespace App\Filament\Supplier\Resources\Quotations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rfq_id')
                    ->relationship('rfq', 'rfq_number', function (Builder $query): Builder {
                        $supplierId = auth()->user()?->supplier?->id;

                        return $query->when(
                            $supplierId,
                            fn (Builder $query, int $supplierId): Builder => $query->where('supplier_id', $supplierId),
                            fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
                        );
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('total_amount_cents')
                    ->label('Total Amount (cents)')
                    ->numeric()
                    ->required()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }
}
