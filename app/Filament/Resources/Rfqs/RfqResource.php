<?php

namespace App\Filament\Resources\Rfqs;

use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Filament\Resources\Rfqs\Pages\EditRfq;
use App\Filament\Resources\Rfqs\Pages\ListRfqs;
use App\Filament\Resources\Rfqs\Schemas\RfqForm;
use App\Filament\Resources\Rfqs\Tables\RfqsTable;
use App\Models\Rfq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RfqResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'orders';

    protected static ?string $model = Rfq::class;

    protected static ?string $recordTitleAttribute = 'rfq_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturer';

    protected static ?string $navigationParentItem = 'Orders';

    protected static ?string $slug = 'manufacturer/rfqs';

    protected static ?int $navigationSort = 401;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'rfq_number',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['buyer', 'supplier']);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (! $record instanceof Rfq) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'Buyer' => (string) ($record->buyer?->name ?? '—'),
            'Supplier' => (string) ($record->supplier?->name ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return RfqForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RfqsTable::configure($table);
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
            'index' => ListRfqs::route('/'),
            'edit' => EditRfq::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
