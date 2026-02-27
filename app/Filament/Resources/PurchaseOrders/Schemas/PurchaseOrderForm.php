<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('buyer_id')
                    ->label('Buyer')
                    ->relationship('buyer', 'name', fn (Builder $query) => $query->where('type', 'buyer'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('supplier_id', null);
                    }),
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
                    ->disabled(fn (Get $get): bool => blank($get('buyer_id'))),
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
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
                    ->required(),
                Select::make('approved_by')
                    ->relationship('approvedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('blockchain_tx_hash')
                    ->label('Blockchain Tx Hash')
                    ->maxLength(255),
            ]);
    }
}
