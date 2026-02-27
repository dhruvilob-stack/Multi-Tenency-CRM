<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\PurchaseOrder;
use App\Models\Shipment;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function systemOverview(): JsonResponse
    {
        return response()->json([
            'organizations' => Organization::query()->count(),
            'buyers' => Organization::query()->where('type', 'buyer')->count(),
            'manufacturers' => Organization::query()->where('type', 'manufacturer')->count(),
            'suppliers' => Supplier::query()->count(),
            'open_purchase_orders' => PurchaseOrder::query()->whereNotIn('status', ['completed'])->count(),
            'open_invoices' => Invoice::query()->whereIn('status', ['pending', 'overdue'])->count(),
            'shipments_in_transit' => Shipment::query()->where('status', 'in_transit')->count(),
        ]);
    }

    public function organizationPerformance(Organization $organization): JsonResponse
    {
        $purchaseOrders = $organization->purchaseOrdersAsBuyer()->count();
        $activeSuppliers = $organization->suppliers()->where('status', 'active')->count();
        $inventoryItems = $organization->inventoryItems()->count();
        $productionOrders = $organization->productionOrders()->count();

        return response()->json([
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'type' => $organization->type,
                'esg_score' => $organization->esg_score,
            ],
            'metrics' => [
                'purchase_orders' => $purchaseOrders,
                'active_suppliers' => $activeSuppliers,
                'inventory_items' => $inventoryItems,
                'production_orders' => $productionOrders,
            ],
        ]);
    }

    public function purchaseOrderFlow(PurchaseOrder $purchaseOrder): JsonResponse
    {
        return response()->json([
            'purchase_order' => [
                'id' => $purchaseOrder->id,
                'order_number' => $purchaseOrder->order_number,
                'status' => $purchaseOrder->status,
                'buyer' => $purchaseOrder->buyer?->name,
                'supplier' => $purchaseOrder->supplier?->name,
                'total_amount_cents' => $purchaseOrder->total_amount_cents,
                'blockchain_tx_hash' => $purchaseOrder->blockchain_tx_hash,
            ],
            'linked_entities' => [
                'items' => $purchaseOrder->items()->count(),
                'grns' => $purchaseOrder->grns()->count(),
                'invoices' => $purchaseOrder->invoices()->count(),
                'shipments' => $purchaseOrder->shipments()->count(),
                'dispatch_orders' => $purchaseOrder->dispatchOrders()->count(),
            ],
            'allowed_status_flow' => [
                'draft',
                'submitted',
                'approved',
                'in_production',
                'dispatched',
                'delivered',
                'completed',
            ],
        ]);
    }
}
