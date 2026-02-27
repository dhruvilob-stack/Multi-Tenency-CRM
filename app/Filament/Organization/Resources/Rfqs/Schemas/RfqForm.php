<?php

namespace App\Filament\Organization\Resources\Rfqs\Schemas;

use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RfqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('buyer_id')
                    ->label('Buyer')
                    ->relationship('buyer', 'name', fn (Builder $query) => $query->where('type', 'buyer'))
                    ->default(auth()->user()?->organization_id)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(function (): array {
                        $buyerId = auth()->user()?->organization_id;

                        if (! $buyerId) {
                            return [];
                        }

                        return Supplier::query()
                            ->where('organization_id', $buyerId)
                            ->where('status', 'active')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (Supplier $supplier): array => [
                                $supplier->id => trim("{$supplier->name} ({$supplier->email})"),
                            ])
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                TextInput::make('rfq_number')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Auto generated'),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'submitted' => 'Submitted',
                        'closed' => 'Closed',
                    ])
                    ->default('open')
                    ->required(),
                TextInput::make('due_date')
                    ->type('date'),
            ]);
    }
}
