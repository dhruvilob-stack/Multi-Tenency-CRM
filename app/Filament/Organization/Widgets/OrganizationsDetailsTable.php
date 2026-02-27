<?php

namespace App\Filament\Organization\Widgets;

use App\Models\Organization;
use App\Support\CrmAccess;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OrganizationsDetailsTable extends TableWidget
{
    protected static ?string $heading = 'Organization Details';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return CrmAccess::canView('organization_dashboard');
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
                TextColumn::make('purchase_orders_as_buyer_count')
                    ->label('Purchase Orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('suppliers_count')
                    ->label('Suppliers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Users')
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
            ->where('type', 'buyer')
            ->withCount(['products', 'purchaseOrdersAsBuyer', 'suppliers', 'users']);

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
