<?php

namespace App\Filament\Organization\Resources\Payments;

use App\Filament\Organization\Resources\Payments\Pages\CreatePayment;
use App\Filament\Organization\Resources\Payments\Pages\EditPayment;
use App\Filament\Organization\Resources\Payments\Pages\ListPayments;
use App\Filament\Organization\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Organization\Resources\Payments\Tables\PaymentsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
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

    protected static string $crmModule = 'org_finance';

    protected static ?string $model = Payment::class;

    protected static ?string $recordTitleAttribute = 'transaction_reference';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Finance';

    protected static ?string $slug = 'organization/payments';

    protected static ?int $navigationSort = 1302;

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

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('invoice.purchaseOrder', fn (Builder $purchaseOrderQuery) => $purchaseOrderQuery->where('buyer_id', (int) $organizationId));
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
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
