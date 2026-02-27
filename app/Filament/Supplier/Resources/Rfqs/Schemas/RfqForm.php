<?php

namespace App\Filament\Supplier\Resources\Rfqs\Schemas;

use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
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
                    ->relationship('buyer', 'name', fn (Builder $query) => $query->where('type', 'buyer'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Hidden::make('supplier_id')
                    ->default(auth()->guard('web')->user()?->organization_id)
                    ->dehydrated()
                    ->required(),
                Placeholder::make('supplier_name')
                    ->label('Supplier')
                    ->content(function ($record): string {
                        $organizationId = $record?->supplier_id ?? auth()->guard('web')->user()?->organization_id;

                        if (! $organizationId) {
                            return '-';
                        }

                        $supplierName = Supplier::query()
                            ->where('organization_id', $organizationId)
                            ->where('status', 'active')
                            ->value('name');

                        if ($supplierName) {
                            return $supplierName;
                        }

                        return $record?->supplier?->name ?? (string) auth()->guard('web')->user()?->organization?->name;
                    }),
                TextInput::make('rfq_number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'submitted' => 'Submitted',
                        'closed' => 'Closed',
                    ])
                    ->required(),
                DatePicker::make('due_date')
                    ->disabled(fn (?Model $record): bool => filled($record)),
            ]);
    }
}
