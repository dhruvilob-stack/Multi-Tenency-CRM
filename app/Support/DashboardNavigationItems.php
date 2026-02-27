<?php

namespace App\Support;

use App\Filament\Organization\Resources\Invoices\InvoiceResource as OrganizationInvoiceResource;
use App\Filament\Organization\Resources\Payments\PaymentResource as OrganizationPaymentResource;
use App\Filament\Organization\Resources\Products\ProductResource as OrganizationProductResource;
use App\Filament\Organization\Resources\PurchaseOrders\PurchaseOrderResource as OrganizationPurchaseOrderResource;
use App\Filament\Organization\Resources\Quotations\QuotationResource as OrganizationQuotationResource;
use App\Filament\Organization\Resources\Rfqs\RfqResource as OrganizationRfqResource;
use App\Filament\Organization\Resources\Suppliers\SupplierResource as OrganizationSupplierResource;
use App\Filament\Organization\Resources\Users\UserResource as OrganizationUserResource;
use App\Filament\Pages\ManufacturerDashboard;
use App\Filament\Pages\OrganizationDashboard;
use App\Filament\Pages\SupplierDashboard;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Resources\QualityReports\QualityReportResource;
use App\Filament\Resources\Rfqs\RfqResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Supplier\Resources\Invoices\InvoiceResource as SupplierInvoiceResource;
use App\Filament\Supplier\Resources\Payments\PaymentResource as SupplierPaymentResource;
use App\Filament\Supplier\Resources\PurchaseOrders\PurchaseOrderResource as SupplierPurchaseOrderResource;
use App\Filament\Supplier\Resources\Quotations\QuotationResource as SupplierQuotationResource;
use App\Filament\Supplier\Resources\Rfqs\RfqResource as SupplierRfqResource;
use App\Filament\Supplier\Resources\Shipments\ShipmentResource as SupplierShipmentResource;
use Filament\Navigation\NavigationItem;
use Throwable;

class DashboardNavigationItems
{
    /**
     * @return array<int, NavigationItem>
     */
    public static function forPage(string $pageClass): array
    {
        $definitions = match ($pageClass) {
            ManufacturerDashboard::class => [
                ['label' => 'Products', 'resource' => ProductResource::class],
                ['label' => 'RFQs', 'resource' => RfqResource::class],
                ['label' => 'Purchase Orders', 'resource' => PurchaseOrderResource::class],
                ['label' => 'Inventory', 'resource' => InventoryItemResource::class],
                ['label' => 'Quality Reports', 'resource' => QualityReportResource::class],
                ['label' => 'Suppliers', 'resource' => SupplierResource::class],
            ],
            OrganizationDashboard::class => [
                ['label' => 'Products', 'resource' => OrganizationProductResource::class],
                ['label' => 'RFQs', 'resource' => OrganizationRfqResource::class],
                ['label' => 'Quotations', 'resource' => OrganizationQuotationResource::class],
                ['label' => 'Purchase Orders', 'resource' => OrganizationPurchaseOrderResource::class],
                ['label' => 'Invoices', 'resource' => OrganizationInvoiceResource::class],
                ['label' => 'Payments', 'resource' => OrganizationPaymentResource::class],
                ['label' => 'Suppliers', 'resource' => OrganizationSupplierResource::class],
                ['label' => 'Users', 'resource' => OrganizationUserResource::class],
            ],
            SupplierDashboard::class => [
                ['label' => 'RFQs', 'resource' => SupplierRfqResource::class],
                ['label' => 'Quotations', 'resource' => SupplierQuotationResource::class],
                ['label' => 'Purchase Orders', 'resource' => SupplierPurchaseOrderResource::class],
                ['label' => 'Invoices', 'resource' => SupplierInvoiceResource::class],
                ['label' => 'Payments', 'resource' => SupplierPaymentResource::class],
                ['label' => 'Shipments', 'resource' => SupplierShipmentResource::class],
            ],
            default => [],
        };

        $items = [];

        foreach ($definitions as $sort => $definition) {
            $resource = $definition['resource'];

            if (! static::isAccessible($resource)) {
                continue;
            }

            $url = static::resourceUrl($resource, 'index');

            if (! is_string($url)) {
                continue;
            }

            $items[] = NavigationItem::make($definition['label'])
                ->url($url)
                ->icon($resource::getNavigationIcon())
                ->activeIcon($resource::getActiveNavigationIcon())
                ->sort($sort)
                ->isActiveWhen(fn (): bool => str_starts_with(url()->current(), rtrim($url, '/')));
        }

        return $items;
    }

    /**
     * @param  class-string  $resource
     */
    private static function isAccessible(string $resource): bool
    {
        if (! method_exists($resource, 'canAccess')) {
            return false;
        }

        return $resource::canAccess();
    }

    /**
     * @param  class-string  $resource
     */
    private static function resourceUrl(string $resource, string $page): ?string
    {
        try {
            $url = $resource::getUrl($page);
        } catch (Throwable) {
            return null;
        }

        return is_string($url) ? $url : null;
    }
}
