<?php

namespace App\Filament\Organization\Resources\Grns;

use App\Filament\Organization\Resources\Grns\Pages\CreateGrn;
use App\Filament\Organization\Resources\Grns\Pages\EditGrn;
use App\Filament\Organization\Resources\Grns\Pages\ListGrns;
use App\Filament\Organization\Resources\Grns\Schemas\GrnForm;
use App\Filament\Organization\Resources\Grns\Tables\GrnsTable;
use App\Filament\Resources\Concerns\AuthorizesCrmResource;
use App\Models\Grn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GrnResource extends Resource
{
    use AuthorizesCrmResource;

    protected static string $crmModule = 'org_operations';

    protected static ?string $model = Grn::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationParentItem = 'Operations';

    protected static ?string $slug = 'organization/grns';

    protected static ?int $navigationSort = 1101;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
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
        if (! $record instanceof Grn) {
            return [];
        }

        return [
            'Status' => (string) $record->status,
            'PO' => (string) ($record->purchaseOrder?->order_number ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return GrnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GrnsTable::configure($table);
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
            'index' => ListGrns::route('/'),
            'create' => CreateGrn::route('/create'),
            'edit' => EditGrn::route('/{record}/edit'),
        ];
    }
}
