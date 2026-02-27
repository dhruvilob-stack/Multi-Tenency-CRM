<?php

namespace App\Filament\Organization\Resources\Rfqs;

use App\Filament\Organization\Resources\Rfqs\Pages\CreateRfq;
use App\Filament\Organization\Resources\Rfqs\Pages\EditRfq;
use App\Filament\Organization\Resources\Rfqs\Pages\ListRfqs;
use App\Filament\Organization\Resources\Rfqs\Schemas\RfqForm;
use App\Filament\Organization\Resources\Rfqs\Tables\RfqsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
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

    protected static string $crmModule = 'org_operations';

    protected static ?string $model = Rfq::class;

    protected static ?string $recordTitleAttribute = 'rfq_number';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Operations';

    protected static ?string $slug = 'organization/rfqs';

    protected static ?int $navigationSort = 1104;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'rfq_number',
            'status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['supplier', 'buyer']);

        if (auth()->user()?->isSuperAdmin()) {
            return $query;
        }

        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('buyer_id', (int) $organizationId);
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
            'create' => CreateRfq::route('/create'),
            'edit' => EditRfq::route('/{record}/edit'),
        ];
    }
}
