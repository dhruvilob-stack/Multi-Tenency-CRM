<?php

namespace App\Filament\Widgets;

use App\Models\Organization;
use App\Support\CrmAccess;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ManufacturerDetailsTable extends TableWidget
{
    protected static ?string $heading = 'Manufacturer Details';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return CrmAccess::canView('manufacturer_dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getQuery())
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'invited',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('production_orders_count')
                    ->label('Production Orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('inventory_items_count')
                    ->label('Inventory Items')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('suppliers_count')
                    ->label('Suppliers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Organization $record): string => "/manufacturer/organizations/{$record->getKey()}/edit"),
            ]);
    }

    private function getQuery(): Builder
    {
        $query = Organization::query()
            ->where('type', 'manufacturer')
            ->withCount(['products', 'productionOrders', 'inventoryItems', 'suppliers']);

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        if (filled($user->organization_id)) {
            return $query->whereKey((int) $user->organization_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
