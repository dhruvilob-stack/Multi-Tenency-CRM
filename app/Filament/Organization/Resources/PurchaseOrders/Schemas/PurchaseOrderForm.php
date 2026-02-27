<?php

namespace App\Filament\Organization\Resources\PurchaseOrders\Schemas;

use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderForm
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
                    ->options(function (Get $get): array {
                        $buyerId = $get('buyer_id');

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
                    ->disabled(fn (Get $get, ?Model $record): bool => blank($get('buyer_id')) || filled($record)),
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => filled($record)),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'in_production' => 'In Production',
                        'dispatched' => 'Dispatched',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                    ])
                    ->required(),
                TextInput::make('total_amount_cents')
                    ->label('Total Amount (cents)')
                    ->numeric()
                    ->required()
                    ->disabled(fn (?Model $record): bool => filled($record)),
            ]);
    }
}
