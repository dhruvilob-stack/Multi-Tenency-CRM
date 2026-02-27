<?php

namespace App\Filament\Supplier\Widgets;

use App\Models\Supplier;
use App\Support\CrmAccess;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class SuppliersDetailsTable extends TableWidget
{
    protected static ?string $heading = 'Supplier Details';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return CrmAccess::canView('supplier_dashboard');
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
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('purchase_orders_count')
                    ->label('Purchase Orders')
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
                    ->url(fn (Supplier $record): string => "/manufacturer/suppliers/{$record->getKey()}/edit"),
            ]);
    }

    private function getQuery(): Builder
    {
        $query = Supplier::query()
            ->with('organization')
            ->withCount(['products', 'purchaseOrders']);

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isSupplier()) {
            $supplierId = $user->supplier?->id;

            if (filled($supplierId)) {
                return $query->whereKey((int) $supplierId);
            }
        }

        if (filled($user->organization_id)) {
            return $query->where('organization_id', (int) $user->organization_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
