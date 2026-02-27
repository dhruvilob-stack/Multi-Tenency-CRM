<?php

namespace App\Filament\Organization\Resources\Products\Tables;

use App\Models\Product;
use App\Support\WorkflowNotifier;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('sku')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('price_cents')
                    ->label('Price')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->modalHeading('Product')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('sku')->placeholder('—'),
                                TextEntry::make('brand.name')->label('Brand'),
                                TextEntry::make('category.name')->label('Category'),
                                TextEntry::make('price_cents')->label('Price')->money('USD', divideBy: 100),
                                TextEntry::make('sustainability_score')->label('Sustainability')->placeholder('—'),
                                TextEntry::make('carbon_kg_per_unit')->label('Carbon (kg/unit)')->placeholder('—'),
                                IconEntry::make('is_active')->label('Active')->boolean(),
                            ])
                            ->columns(2),
                        TextEntry::make('description')->placeholder('—')->prose()->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('requestChange')
                    ->label('Request change')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('primary')
                    ->modalHeading('Request product change')
                    ->schema([
                        Select::make('type')
                            ->label('Change type')
                            ->options([
                                'new_variant' => 'New variant',
                                'price' => 'Price update',
                                'description' => 'Description update',
                                'availability' => 'Availability / active status',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Textarea::make('message')
                            ->label('Message')
                            ->rows(4)
                            ->required(),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $user = auth()->user();
                        $organizationName = (string) ($user?->organization?->name ?? 'Organization');

                        $masterId = $record->master_product_id ?? $record->getKey();

                        WorkflowNotifier::notifyManufacturerAdmins(
                            'Product change requested',
                            "{$organizationName} requested a change for {$record->name}. Type: {$data['type']}. Message: {$data['message']}",
                            "/manufacturer/products?highlight_id={$masterId}",
                            'catalog'
                        );

                        Notification::make()
                            ->title('Request sent')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
