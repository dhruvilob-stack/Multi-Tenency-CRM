<?php

namespace App\Filament\Organization\Resources\Quotations;

use App\Filament\Organization\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Organization\Resources\Quotations\Tables\QuotationsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
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

    protected static string $crmModule = 'org_operations';

    protected static ?string $model = Quotation::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Operations';

    protected static ?string $slug = 'organization/quotations';

    protected static ?int $navigationSort = 1103;

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

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('rfq', fn (Builder $rfqQuery) => $rfqQuery->where('buyer_id', (int) $organizationId));
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
        return $schema;
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
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
