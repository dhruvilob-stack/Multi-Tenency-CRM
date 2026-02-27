<?php

namespace App\Filament\Organization\Resources\Quotations\Tables;

use App\Models\Quotation;
use App\Support\WorkflowNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rfq.rfq_number')
                    ->label('RFQ')
                    ->sortable(),
                TextColumn::make('rfq.supplier.name')
                    ->label('Supplier')
                    ->sortable(),
                TextColumn::make('total_amount_cents')
                    ->label('Total')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                BadgeColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (Quotation $record): bool => $record->status !== 'accepted')
                    ->requiresConfirmation()
                    ->action(function (Quotation $record): void {
                        $rfq = $record->rfq;

                        if (! $rfq) {
                            return;
                        }

                        $record->forceFill(['status' => 'accepted'])->save();

                        Quotation::query()
                            ->where('rfq_id', $rfq->getKey())
                            ->where('id', '!=', $record->getKey())
                            ->update(['status' => 'rejected']);

                        $rfq->forceFill(['status' => 'closed'])->save();

                        WorkflowNotifier::notifySupplierUser(
                            (int) $rfq->supplier_id,
                            'Quotation accepted',
                            "Your quotation for {$rfq->rfq_number} was accepted.",
                            "/supplier/quotations?highlight_id={$record->getKey()}",
                            'orders'
                        );

                        WorkflowNotifier::notifyManufacturerAdmins(
                            'Quotation accepted',
                            "Quotation accepted for {$rfq->rfq_number}.",
                            "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                            'orders'
                        );

                        Notification::make()
                            ->title('Quotation accepted')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
