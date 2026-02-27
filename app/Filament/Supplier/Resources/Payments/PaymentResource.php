<?php

namespace App\Filament\Supplier\Resources\Payments;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Supplier\Resources\Payments\Pages\ListPayments;
use App\Filament\Supplier\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Supplier\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'supplier_finance';

    protected static ?string $model = Payment::class;

    protected static ?string $recordTitleAttribute = 'transaction_reference';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Supplier';

    protected static ?string $navigationParentItem = 'Finance';

    protected static ?string $slug = 'supplier/payments';

    protected static ?int $navigationSort = 2002;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'transaction_reference',
            'status',
            'payment_mode',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['invoice.purchaseOrder']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $supplierId = auth()->user()?->supplier?->id;

        if (! $supplierId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('invoice.purchaseOrder', fn (Builder $purchaseOrderQuery) => $purchaseOrderQuery->where('supplier_id', (int) $supplierId));
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Payment) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'Invoice' => (string) ($record->invoice?->invoice_number ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
