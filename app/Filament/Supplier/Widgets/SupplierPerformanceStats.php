<?php

namespace App\Filament\Supplier\Widgets;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupplierPerformanceStats extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $supplier = auth()->user()?->supplier;
        $productCount = $supplier ? $supplier->products()->count() : 0;
        $supplierId = $supplier?->id;

        $openRfqs = Rfq::query()
            ->where('status', 'open')
            ->when($supplierId, fn ($query, int $supplierId) => $query->where('supplier_id', $supplierId))
            ->count();

        $quotations = Quotation::query()
            ->when($supplierId, function ($query, int $supplierId) {
                $query->whereHas('rfq', fn ($rfqQuery) => $rfqQuery->where('supplier_id', $supplierId));
            })
            ->count();

        $shipments = Shipment::query()
            ->where('status', 'in_transit')
            ->when($supplierId, function ($query, int $supplierId) {
                $query->whereHas('purchaseOrder', fn ($poQuery) => $poQuery->where('supplier_id', $supplierId));
            })
            ->count();

        $openInvoices = Invoice::query()
            ->whereIn('status', ['pending', 'overdue'])
            ->when($supplierId, function ($query, int $supplierId) {
                $query->whereHas('purchaseOrder', fn ($poQuery) => $poQuery->where('supplier_id', $supplierId));
            })
            ->count();

        return [
            Stat::make('Products Supplied', $productCount)
                ->icon('heroicon-m-cube')
                ->color('warning'),
            Stat::make('Open RFQs', $openRfqs)
                ->icon('heroicon-m-inbox')
                ->color('info'),
            Stat::make('Quotations', $quotations)
                ->icon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Shipments In Transit', $shipments)
                ->icon('heroicon-m-truck')
                ->color('success'),
            Stat::make('Open Invoices', $openInvoices)
                ->icon('heroicon-m-receipt-refund')
                ->color('danger'),
            Stat::make('Status', $supplier?->status ?? 'Not linked')
                ->icon('heroicon-m-sparkles')
                ->color('gray'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('dashboard.supplier_performance');
    }
}
