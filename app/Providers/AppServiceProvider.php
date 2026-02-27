<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DispatchOrder;
use App\Models\Grn;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Shipment;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Support\MasterCatalogSync;
use App\Support\WorkflowNotifier;
use Filament\Facades\Filament;
use Filament\Resources\Events\RecordSaved;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::creating(function (Product $product): void {
            if (filled($product->master_product_id)) {
                return;
            }

            if (filled($product->sku)) {
                return;
            }

            $product->sku = $this->nextSerial(
                Product::class,
                'sku',
                'SKU-'.strtoupper((string) $product->organization_id).'-',
                5
            );
        });

        Product::created(function (Product $product): void {
            if (! $this->isMasterCatalogRecord($product, $product->master_product_id)) {
                return;
            }

            $sync = app(MasterCatalogSync::class);
            $sync->syncProduct($product);

            $cloneIdsByOrganization = Product::query()
                ->withoutGlobalScope('organization')
                ->where('master_product_id', $product->getKey())
                ->pluck('id', 'organization_id')
                ->all();

            $actor = $this->describeActor();

            WorkflowNotifier::notifySuperAdmins(
                'Catalog updated',
                "{$product->name} was added to catalog.".($actor ? " {$actor}" : ''),
                "/manufacturer/products?highlight_id={$product->getKey()}",
                'catalog'
            );

            Organization::query()
                ->where('type', 'buyer')
                ->pluck('id')
                ->each(function (int $organizationId) use ($product, $cloneIdsByOrganization): void {
                    $cloneId = $cloneIdsByOrganization[$organizationId] ?? null;

                    if (! $cloneId) {
                        return;
                    }

                    WorkflowNotifier::notifyOrganizationAdmins(
                        $organizationId,
                        'Catalog updated',
                        "{$product->name} is now available in catalog.",
                        "/organization/products?highlight_id={$cloneId}",
                        'catalog'
                    );
                });

            WorkflowNotifier::notifyManufacturerAdmins(
                'Catalog updated',
                "{$product->name} was added to catalog.".($actor ? " {$actor}" : ''),
                "/manufacturer/products?highlight_id={$product->getKey()}",
                'catalog'
            );
        });

        Product::updated(function (Product $product): void {
            if (! $this->isMasterCatalogRecord($product, $product->master_product_id)) {
                return;
            }

            if (! $product->wasChanged()) {
                return;
            }

            $changedFields = array_values(array_diff(array_keys($product->getChanges()), ['updated_at']));

            if (blank($changedFields)) {
                return;
            }

            $actor = $this->describeActor();
            $changedFieldsLabel = ' Fields: '.implode(', ', $changedFields).'.';

            $sync = app(MasterCatalogSync::class);
            $sync->syncProduct($product);

            $cloneIdsByOrganization = Product::query()
                ->withoutGlobalScope('organization')
                ->where('master_product_id', $product->getKey())
                ->pluck('id', 'organization_id')
                ->all();

            $affectedBuyerIds = PurchaseOrderItem::query()
                ->whereIn('product_id', array_values($cloneIdsByOrganization))
                ->whereHas('purchaseOrder', fn ($query) => $query->where('status', '!=', 'completed'))
                ->with(['purchaseOrder'])
                ->get()
                ->pluck('purchaseOrder.buyer_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            foreach ($affectedBuyerIds as $buyerId) {
                $cloneId = $cloneIdsByOrganization[$buyerId] ?? null;

                if (! $cloneId) {
                    continue;
                }

                WorkflowNotifier::notifyOrganizationAdmins(
                    (int) $buyerId,
                    'Catalog updated',
                    "{$product->name} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                    "/organization/products?highlight_id={$cloneId}",
                    'catalog'
                );
            }

            WorkflowNotifier::notifyManufacturerAdmins(
                'Product updated',
                "{$product->name} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/products?highlight_id={$product->getKey()}",
                'catalog'
            );
        });

        Brand::saved(function (Brand $brand): void {
            if (! $this->isMasterCatalogRecord($brand, $brand->master_brand_id ?? null)) {
                return;
            }

            app(MasterCatalogSync::class)->syncBrand($brand);
        });

        Category::saved(function (Category $category): void {
            if (! $this->isMasterCatalogRecord($category, $category->master_category_id ?? null)) {
                return;
            }

            app(MasterCatalogSync::class)->syncCategory($category);
        });

        Rfq::creating(function (Rfq $rfq): void {
            if (filled($rfq->rfq_number)) {
                return;
            }

            $rfq->rfq_number = $this->nextSerial(
                Rfq::class,
                'rfq_number',
                'RFQ-'.now()->format('Y').'-',
                5
            );
        });

        Rfq::created(function (Rfq $rfq): void {
            $actor = $this->describeActor();

            WorkflowNotifier::notifySupplierUser(
                (int) $rfq->supplier_id,
                'New RFQ received',
                "RFQ {$rfq->rfq_number} received from {$rfq->buyer?->name}.".($actor ? " {$actor}" : ''),
                "/supplier/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyOrganizationAdmins(
                (int) $rfq->buyer_id,
                'RFQ created',
                "RFQ {$rfq->rfq_number} sent to {$rfq->supplier?->name}. ".($actor ? " {$actor}" : ''),
                "/organization/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'RFQ created',
                "RFQ {$rfq->rfq_number} created. Buyer: {$rfq->buyer?->name}. Supplier: {$rfq->supplier?->name}. ".($actor ? " {$actor}" : ''),
                "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'RFQ created',
                "RFQ {$rfq->rfq_number} created. Buyer: {$rfq->buyer?->name}. Supplier: {$rfq->supplier?->name}. ".($actor ? " {$actor}" : ''),
                "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );
        });

        Rfq::updated(function (Rfq $rfq): void {
            if (! $rfq->wasChanged()) {
                return;
            }

            $changedFields = array_values(array_diff(array_keys($rfq->getChanges()), ['updated_at']));

            if (blank($changedFields)) {
                return;
            }

            $actor = $this->describeActor();
            $changedFieldsLabel = ' Fields: '.implode(', ', $changedFields).'.';

            WorkflowNotifier::notifySupplierUser(
                (int) $rfq->supplier_id,
                'RFQ updated',
                "RFQ {$rfq->rfq_number} updated. Status: {$rfq->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/supplier/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyOrganizationAdmins(
                (int) $rfq->buyer_id,
                'RFQ updated',
                "RFQ {$rfq->rfq_number} updated. Status: {$rfq->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/organization/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'RFQ updated',
                "RFQ {$rfq->rfq_number} updated. Status: {$rfq->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );
        });

        Quotation::created(function (Quotation $quotation): void {
            $rfq = $quotation->rfq;

            if (! $rfq) {
                return;
            }

            $actor = $this->describeActor();

            WorkflowNotifier::notifyOrganizationAdmins(
                (int) $rfq->buyer_id,
                'New quotation received',
                "Quotation submitted for {$rfq->rfq_number}. Total: {$quotation->total_amount_cents} cents.".($actor ? " {$actor}" : ''),
                "/organization/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Quotation submitted',
                "Quotation submitted for {$rfq->rfq_number}. Buyer: {$rfq->buyer?->name}. Supplier: {$rfq->supplier?->name}.".($actor ? " {$actor}" : ''),
                "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );
        });

        Quotation::updated(function (Quotation $quotation): void {
            if (! $quotation->wasChanged()) {
                return;
            }

            $rfq = $quotation->rfq;

            if (! $rfq) {
                return;
            }

            $changedFields = array_values(array_diff(array_keys($quotation->getChanges()), ['updated_at']));
            $changedFieldsLabel = filled($changedFields) ? ' Fields: '.implode(', ', $changedFields).'.' : '';
            $actor = $this->describeActor();

            WorkflowNotifier::notifyOrganizationAdmins(
                (int) $rfq->buyer_id,
                'Quotation updated',
                "Quotation for {$rfq->rfq_number} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/organization/quotations?highlight_id={$quotation->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Quotation updated',
                "Quotation for {$rfq->rfq_number} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/rfqs?highlight_id={$rfq->getKey()}",
                'orders'
            );
        });

        InventoryItem::created(function (InventoryItem $inventoryItem): void {
            $actor = $this->describeActor();

            WorkflowNotifier::notifyOrganizationAdmins(
                (int) $inventoryItem->organization_id,
                'Inventory item created',
                "{$inventoryItem->product?->name} inventory item created.".($actor ? " {$actor}" : ''),
                "/organization/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Inventory item created',
                "{$inventoryItem->product?->name} inventory item created.".($actor ? " {$actor}" : ''),
                "/manufacturer/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Inventory item created',
                "{$inventoryItem->product?->name} inventory item created.".($actor ? " {$actor}" : ''),
                "/manufacturer/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );
        });

        InventoryItem::updated(function (InventoryItem $inventoryItem): void {
            if (! $inventoryItem->wasChanged()) {
                return;
            }

            $actor = $this->describeActor();
            $changedFields = array_values(array_diff(array_keys($inventoryItem->getChanges()), ['updated_at']));
            $changedFieldsLabel = filled($changedFields) ? ' Fields: '.implode(', ', $changedFields).'.' : '';

            WorkflowNotifier::notifyOrganizationAdmins(
                $inventoryItem->organization_id,
                'Inventory updated',
                "{$inventoryItem->product?->name} inventory updated to {$inventoryItem->quantity_on_hand}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/organization/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Inventory updated',
                "{$inventoryItem->product?->name} inventory updated to {$inventoryItem->quantity_on_hand}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Inventory updated',
                "{$inventoryItem->product?->name} inventory updated to {$inventoryItem->quantity_on_hand}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/inventory-items?highlight_id={$inventoryItem->getKey()}",
                'orders'
            );
        });

        ProductionOrder::creating(function (ProductionOrder $productionOrder): void {
            if (filled($productionOrder->order_number)) {
                return;
            }

            $productionOrder->order_number = $this->nextSerial(
                ProductionOrder::class,
                'order_number',
                'PRD-'.now()->format('Y').'-',
                5
            );
        });

        DispatchOrder::creating(function (DispatchOrder $dispatchOrder): void {
            if (filled($dispatchOrder->dispatch_number)) {
                return;
            }

            $dispatchOrder->dispatch_number = $this->nextSerial(
                DispatchOrder::class,
                'dispatch_number',
                'DSP-'.now()->format('Y').'-',
                5
            );
        });

        PurchaseOrder::creating(function (PurchaseOrder $purchaseOrder): void {
            if (filled($purchaseOrder->order_number)) {
                return;
            }

            $purchaseOrder->order_number = $this->nextSerial(
                PurchaseOrder::class,
                'order_number',
                'PO-'.now()->format('Y').'-',
                5
            );
        });

        Payment::creating(function (Payment $payment): void {
            if (filled($payment->transaction_reference)) {
                return;
            }

            $payment->transaction_reference = $this->nextSerial(
                Payment::class,
                'transaction_reference',
                'PAY-'.now()->format('Ymd').'-',
                5
            );
        });

        ProductionOrder::saving(function (ProductionOrder $productionOrder): void {
            if ($productionOrder->quantity_planned <= 0) {
                return;
            }

            if (blank($productionOrder->machine_utilization_percent)) {
                $productionOrder->machine_utilization_percent = round(
                    ($productionOrder->quantity_completed / $productionOrder->quantity_planned) * 100,
                    2
                );
            }

            if (blank($productionOrder->defect_ratio_percent)) {
                $defects = max($productionOrder->quantity_planned - $productionOrder->quantity_completed, 0);
                $productionOrder->defect_ratio_percent = round(
                    ($defects / $productionOrder->quantity_planned) * 100,
                    2
                );
            }
        });

        PurchaseOrderItem::saving(function (PurchaseOrderItem $item): void {
            if ($item->price_cents > 0) {
                return;
            }

            $item->price_cents = (int) ($item->product?->price_cents ?? 0);
        });

        PurchaseOrderItem::saved(function (PurchaseOrderItem $item): void {
            $this->recalculatePurchaseOrderTotal($item->purchaseOrder);
        });

        PurchaseOrderItem::deleted(function (PurchaseOrderItem $item): void {
            $this->recalculatePurchaseOrderTotal($item->purchaseOrder);
        });

        PurchaseOrder::created(function (PurchaseOrder $purchaseOrder): void {
            $actor = $this->describeActor();

            WorkflowNotifier::notifySupplierUser(
                $purchaseOrder->supplier_id,
                'New purchase order assigned',
                "PO {$purchaseOrder->order_number} was created for your supplier account.".($actor ? " {$actor}" : ''),
                "/supplier/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyOrganizationAdmins(
                $purchaseOrder->buyer_id,
                'Purchase order created',
                "PO {$purchaseOrder->order_number} is created and awaiting processing.".($actor ? " {$actor}" : ''),
                "/organization/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Purchase order created',
                "PO {$purchaseOrder->order_number} has been created.".($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Purchase order created',
                "PO {$purchaseOrder->order_number} has been created.".($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );
        });

        PurchaseOrder::updated(function (PurchaseOrder $purchaseOrder): void {
            if (! $purchaseOrder->wasChanged()) {
                return;
            }

            $actor = $this->describeActor();

            $changedFields = array_values(array_diff(array_keys($purchaseOrder->getChanges()), ['updated_at']));
            $changedFieldsLabel = filled($changedFields) ? ' Fields: '.implode(', ', $changedFields).'.' : '';

            WorkflowNotifier::notifySupplierUser(
                $purchaseOrder->supplier_id,
                'Purchase order updated',
                "PO {$purchaseOrder->order_number} updated. Status: {$purchaseOrder->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/supplier/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyOrganizationAdmins(
                $purchaseOrder->buyer_id,
                'Purchase order updated',
                "PO {$purchaseOrder->order_number} updated. Status: {$purchaseOrder->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/organization/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Purchase order updated',
                "PO {$purchaseOrder->order_number} updated. Status: {$purchaseOrder->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Purchase order updated',
                "PO {$purchaseOrder->order_number} updated. Status: {$purchaseOrder->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );
        });

        Shipment::created(function (Shipment $shipment): void {
            $purchaseOrder = $shipment->purchaseOrder;

            if (! $purchaseOrder) {
                return;
            }

            $actor = $this->describeActor();

            WorkflowNotifier::notifyOrganizationAdmins(
                $purchaseOrder->buyer_id,
                'Shipment created',
                "Shipment {$shipment->tracking_number} created for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/organization/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Shipment created',
                "Shipment {$shipment->tracking_number} created for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/manufacturer/dispatch-orders?highlight_id={$shipment->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Shipment created',
                "Shipment {$shipment->tracking_number} created for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/manufacturer/dispatch-orders?highlight_id={$shipment->getKey()}",
                'orders'
            );
        });

        Shipment::updated(function (Shipment $shipment): void {
            if (! $shipment->wasChanged()) {
                return;
            }

            $purchaseOrder = $shipment->purchaseOrder;

            if (! $purchaseOrder) {
                return;
            }

            $actor = $this->describeActor();
            $changedFields = array_values(array_diff(array_keys($shipment->getChanges()), ['updated_at']));
            $changedFieldsLabel = filled($changedFields) ? ' Fields: '.implode(', ', $changedFields).'.' : '';

            WorkflowNotifier::notifyOrganizationAdmins(
                $purchaseOrder->buyer_id,
                'Shipment updated',
                "Shipment {$shipment->tracking_number} updated. Status: {$shipment->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/organization/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Shipment updated',
                "Shipment {$shipment->tracking_number} updated. Status: {$shipment->status}.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/dispatch-orders?highlight_id={$shipment->getKey()}",
                'orders'
            );
        });

        Grn::created(function (Grn $grn): void {
            $purchaseOrder = $grn->purchaseOrder;

            if (! $purchaseOrder) {
                return;
            }

            $actor = $this->describeActor();

            WorkflowNotifier::notifySuperAdmins(
                'GRN recorded',
                "GRN recorded for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'GRN recorded',
                "GRN recorded for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySupplierUser(
                $purchaseOrder->supplier_id,
                'GRN recorded',
                "Buyer recorded GRN for PO {$purchaseOrder->order_number}.".($actor ? " {$actor}" : ''),
                "/supplier/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );
        });

        Grn::updated(function (Grn $grn): void {
            if (! $grn->wasChanged()) {
                return;
            }

            $purchaseOrder = $grn->purchaseOrder;

            if (! $purchaseOrder) {
                return;
            }

            $changedFields = array_values(array_diff(array_keys($grn->getChanges()), ['updated_at']));
            $changedFieldsLabel = filled($changedFields) ? ' Fields: '.implode(', ', $changedFields).'.' : '';
            $actor = $this->describeActor();

            WorkflowNotifier::notifySuperAdmins(
                'GRN updated',
                "GRN for PO {$purchaseOrder->order_number} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'GRN updated',
                "GRN for PO {$purchaseOrder->order_number} updated.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/manufacturer/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );

            WorkflowNotifier::notifySupplierUser(
                $purchaseOrder->supplier_id,
                'GRN updated',
                "GRN for PO {$purchaseOrder->order_number} was updated by buyer.".$changedFieldsLabel.($actor ? " {$actor}" : ''),
                "/supplier/purchase-orders?highlight_id={$purchaseOrder->getKey()}",
                'orders'
            );
        });

        SupplierInvitation::created(function (SupplierInvitation $invitation): void {
            WorkflowNotifier::notifyOrganizationAdmins(
                $invitation->organization_id,
                'Supplier invited',
                "Invitation sent to {$invitation->email}.",
                '/manufacturer/suppliers',
                'catalog'
            );

            WorkflowNotifier::notifySuperAdmins(
                'Supplier invited',
                "Invitation sent to {$invitation->email}.",
                '/manufacturer/suppliers',
                'catalog'
            );

            WorkflowNotifier::notifyManufacturerAdmins(
                'Supplier invited',
                "Invitation sent to {$invitation->email}.",
                '/manufacturer/suppliers',
                'catalog'
            );
        });

        Event::listen(RecordSaved::class, function (): void {
            if (app()->runningInConsole()) {
                return;
            }

            $args = func_get_args();
            $event = $args[0] ?? null;

            if (! $event instanceof RecordSaved) {
                return;
            }

            $record = $event->getRecord();
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $skipModels = [
                InventoryItem::class,
                PurchaseOrder::class,
                Shipment::class,
                Grn::class,
                Rfq::class,
                Quotation::class,
                SupplierInvitation::class,
            ];

            if (in_array($record::class, $skipModels, true)) {
                return;
            }

            $action = $event->getPage() instanceof CreateRecord ? 'created' : 'updated';
            $modelLabel = class_basename($record);
            $roleSlug = $this->getUserRoleSlug($user);
            $roleLabel = $this->getRoleLabel($roleSlug);

            $title = "{$roleLabel} {$modelLabel} {$action}";
            $body = "{$user->name} ({$roleLabel}) {$action} {$modelLabel} (#{$record->getKey()})";

            $resource = $event->getPage()::getResource();
            $permissionModule = $this->resolveCrmModuleFromResource($resource);
            $pageName = $event->getPage()::getResourcePageName();
            $panel = Filament::getCurrentPanel();
            $url = null;

            try {
                $url = $resource::getUrl($pageName, ['record' => $record], true, $panel, null, true);
            } catch (Throwable) {
                $url = null;
            }

            $highlightUrl = null;

            try {
                $highlightUrl = $resource::getUrl('index', [], true, $panel, null, true);

                if (filled($highlightUrl)) {
                    $highlightUrl .= str_contains($highlightUrl, '?') ? '&' : '?';
                    $highlightUrl .= 'highlight_id='.$record->getKey();
                }
            } catch (Throwable) {
                $highlightUrl = null;
            }

            $navigationGroup = $resource::getNavigationGroup();
            $section = is_string($navigationGroup) && filled($navigationGroup)
                ? Str::slug($navigationGroup, '-')
                : 'activity';

            $actionUrl = $highlightUrl ?: $url;

            WorkflowNotifier::notifySuperAdmins($title, $body, $actionUrl, $section, $permissionModule);
            WorkflowNotifier::notifyManufacturerAdmins($title, $body, $actionUrl, $section, $permissionModule);

            $organizationId = $this->resolveOrganizationId($record);

            if (! $organizationId && $roleSlug === 'supplier' && filled($user->organization_id)) {
                $organizationId = (int) $user->organization_id;
            }

            if ($organizationId) {
                WorkflowNotifier::notifyOrganizationAdmins($organizationId, $title, $body, $actionUrl, $section, $permissionModule);
            }

            if ($supplierId = $this->resolveSupplierId($record)) {
                WorkflowNotifier::notifySupplierUser($supplierId, $title, $body, $actionUrl, $section, $permissionModule);
            }

            $this->notifyAdditionalRoles($roleSlug, $title, $body, $actionUrl, $section);
        });

        DatabaseNotification::retrieved(function (DatabaseNotification $notification): void {
            $icon = Arr::get($notification->data, 'icon');

            if ($icon === 'heroicon-s-bell-ring') {
                $notification->data = array_merge($notification->data, [
                    'icon' => 'heroicon-s-bell',
                ]);
            }
        });
    }

    private function describeActor(): ?string
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        $roleSlug = $this->getUserRoleSlug($user);
        $roleLabel = $this->getRoleLabel($roleSlug, $user);

        return "Updated by {$user->name} ({$roleLabel}).";
    }

    private function getUserRoleSlug(User $user): string
    {
        $role = $user->role?->name ?? $user->role;

        return is_string($role) && filled($role) ? $role : 'user';
    }

    private function getRoleLabel(string $roleSlug, User $user): string
    {
        if ($roleSlug === 'organization_admin' && $user->organization?->type === 'manufacturer') {
            return 'Manufacturer';
        }

        return match ($roleSlug) {
            'super_admin' => 'Manufacturer',
            'organization_admin' => 'Organization',
            'supplier' => 'Supplier',
            default => Str::title(str_replace('_', ' ', $roleSlug)),
        };
    }

    private function resolveOrganizationId(Model $record): ?int
    {
        if ($organizationId = $record->getAttribute('organization_id')) {
            return (int) $organizationId;
        }

        if ($buyerId = $record->getAttribute('buyer_id')) {
            return (int) $buyerId;
        }

        if (method_exists($record, 'organization')) {
            return optional($record->organization)->id;
        }

        return null;
    }

    private function resolveSupplierId(Model $record): ?int
    {
        if ($supplierId = $record->getAttribute('supplier_id')) {
            return (int) $supplierId;
        }

        if (method_exists($record, 'supplier')) {
            return optional($record->supplier)->id;
        }

        return null;
    }

    private function isMasterCatalogRecord(Model $record, mixed $masterId): bool
    {
        if (filled($masterId)) {
            return false;
        }

        if (! method_exists($record, 'organization')) {
            return false;
        }

        $organization = $record->organization;

        return $organization instanceof Organization && $organization->type === 'manufacturer';
    }

    private function notifyAdditionalRoles(string $actorRoleSlug, string $title, string $body, ?string $url, ?string $section): void
    {
        // Reserved for future expansion (e.g. notifying auditors, managers, etc.).
    }

    private function resolveCrmModuleFromResource(string $resource): ?string
    {
        if (! class_exists($resource) || ! property_exists($resource, 'crmModule')) {
            return null;
        }

        try {
            $reflection = new \ReflectionProperty($resource, 'crmModule');
            $reflection->setAccessible(true);
            $value = $reflection->getValue();
        } catch (Throwable) {
            return null;
        }

        return is_string($value) && filled($value) ? $value : null;
    }

    private function recalculatePurchaseOrderTotal(?PurchaseOrder $purchaseOrder): void
    {
        if (! $purchaseOrder) {
            return;
        }

        $total = $purchaseOrder->items()
            ->get()
            ->sum(fn (PurchaseOrderItem $item): int => (int) $item->quantity * (int) $item->price_cents);

        if ((int) $purchaseOrder->total_amount_cents === (int) $total) {
            return;
        }

        $purchaseOrder->forceFill([
            'total_amount_cents' => (int) $total,
        ])->saveQuietly();
    }

    private function nextSerial(string $modelClass, string $column, string $prefix, int $padding = 5): string
    {
        $lastValue = $modelClass::query()
            ->where($column, 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value($column);

        $lastNumber = 0;

        if (is_string($lastValue) && str_starts_with($lastValue, $prefix)) {
            $numericPart = substr($lastValue, strlen($prefix));
            $lastNumber = (int) preg_replace('/\D/', '', $numericPart);
        }

        return $prefix.str_pad((string) ($lastNumber + 1), $padding, '0', STR_PAD_LEFT);
    }
}
