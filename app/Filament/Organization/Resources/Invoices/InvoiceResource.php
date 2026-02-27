<?php

namespace App\Filament\Organization\Resources\Invoices;

use App\Filament\Organization\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Organization\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Organization\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Organization\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Organization\Resources\Invoices\Tables\InvoicesTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InvoiceResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_finance';

    protected static ?string $model = Invoice::class;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-receipt-refund';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Finance';

    protected static ?string $slug = 'organization/invoices';

    protected static ?int $navigationSort = 1301;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'invoice_number',
            'external_reference',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['purchaseOrder']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('purchaseOrder', fn (Builder $purchaseOrderQuery) => $purchaseOrderQuery->where('buyer_id', (int) $organizationId));
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Invoice) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'PO' => (string) ($record->purchaseOrder?->order_number ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
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
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
