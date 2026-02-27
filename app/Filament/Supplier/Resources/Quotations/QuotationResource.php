<?php

namespace App\Filament\Supplier\Resources\Quotations;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Supplier\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Supplier\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Supplier\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Supplier\Resources\Quotations\Schemas\QuotationForm;
use App\Filament\Supplier\Resources\Quotations\Tables\QuotationsTable;
use App\Models\Quotation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuotationResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'supplier_sales';

    protected static ?string $model = Quotation::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Supplier';

    protected static ?string $navigationParentItem = 'Sales';

    protected static ?string $slug = 'supplier/quotations';

    protected static ?int $navigationSort = 2201;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['rfq.buyer', 'rfq.supplier']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $supplierId = auth()->user()?->supplier?->id;

        if (! $supplierId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('rfq', fn (Builder $rfqQuery) => $rfqQuery->where('supplier_id', (int) $supplierId));
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Quotation) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'RFQ' => (string) ($record->rfq?->rfq_number ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
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
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }
}
