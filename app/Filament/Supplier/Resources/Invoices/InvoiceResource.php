<?php

namespace App\Filament\Supplier\Resources\Invoices;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Supplier\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Supplier\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Supplier\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Supplier\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Supplier\Resources\Invoices\Tables\InvoicesTable;
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

    protected static string $crmModule = 'supplier_finance';

    protected static ?string $model = Invoice::class;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-receipt-refund';

    protected static string|\UnitEnum|null $navigationGroup = 'Supplier';

    protected static ?string $navigationParentItem = 'Finance';

    protected static ?string $slug = 'supplier/invoices';

    protected static ?int $navigationSort = 2001;

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

        $supplierId = auth()->user()?->supplier?->id;

        if (! $supplierId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('purchaseOrder', fn (Builder $purchaseOrderQuery) => $purchaseOrderQuery->where('supplier_id', (int) $supplierId));
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
