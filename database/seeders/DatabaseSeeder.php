<?php

namespace Database\Seeders;

use App\Models\BomItem;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DispatchOrder;
use App\Models\Grn;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\OrganizationRevenueSnapshot;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Quotation;
use App\Models\RawMaterial;
use App\Models\Rfq;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdminRole = Role::query()->firstOrCreate(
            ['name' => 'super_admin'],
            ['permissions' => ['*']]
        );

        $manufacturerRole = Role::query()->firstOrCreate(
            ['name' => 'manufacturer'],
            ['permissions' => ['manufacturing.manage', 'products.manage', 'inventory.manage', 'purchase_orders.manage']]
        );

        $organizationAdminRole = Role::query()->firstOrCreate(
            ['name' => 'organization_admin'],
            ['permissions' => ['organizations.manage', 'catalog.manage', 'purchase_orders.manage', 'invoices.manage', 'payments.manage']]
        );

        $supplierRole = Role::query()->firstOrCreate(
            ['name' => 'supplier'],
            ['permissions' => ['quotations.manage', 'shipments.manage', 'invoices.manage']]
        );

        $manufacturerOrganization = Organization::query()->updateOrCreate(
            ['slug' => 'manufacturer-hq'],
            [
                'name' => 'Manufacturer HQ',
                'tenant_code' => 'MFG-HQ',
                'type' => 'manufacturer',
                'status' => 'active',
                'currency_code' => 'USD',
                'esg_score' => 88.40,
            ]
        );

        $organizationOne = Organization::query()->updateOrCreate(
            ['slug' => 'anakrani-industries'],
            [
                'name' => 'Anakrani Industries',
                'tenant_code' => 'ORG-ANA',
                'type' => 'buyer',
                'status' => 'active',
                'currency_code' => 'USD',
                'esg_score' => 81.15,
            ]
        );

        $organizationTwo = Organization::query()->updateOrCreate(
            ['slug' => 'dhnakrani-trading'],
            [
                'name' => 'Dhnakrani Trading',
                'tenant_code' => 'ORG-DHN',
                'type' => 'buyer',
                'status' => 'active',
                'currency_code' => 'USD',
                'esg_score' => 77.80,
            ]
        );

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'dhruvilnakrani.ob@gmail.com'],
            [
                'name' => 'Manufacturer Admin',
                'role_id' => $superAdminRole->id,
                'role' => 'super_admin',
                'organization_id' => null,
                'password' => bcrypt('password'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'nakranidhruvil77@gmail.com'],
            [
                'name' => 'Master Admin',
                'role_id' => $superAdminRole->id,
                'role' => 'super_admin',
                'organization_id' => null,
                'password' => bcrypt('password'),
            ]
        );

        $organizationAdminOne = User::query()->updateOrCreate(
            ['email' => 'anakrani29@gmail.com'],
            [
                'name' => 'Organization Admin (Anakrani)',
                'role_id' => $organizationAdminRole->id,
                'role' => 'organization_admin',
                'organization_id' => $organizationOne->id,
                'password' => bcrypt('password'),
            ]
        );

        $organizationAdminTwo = User::query()->updateOrCreate(
            ['email' => 'dhnakrani80@gmail.com'],
            [
                'name' => 'Organization Admin (Dhnakrani)',
                'role_id' => $organizationAdminRole->id,
                'role' => 'organization_admin',
                'organization_id' => $organizationTwo->id,
                'password' => bcrypt('password'),
            ]
        );

        $supplierUserOne = User::query()->updateOrCreate(
            ['email' => 'dhruvcmdzone22@gmail.com'],
            [
                'name' => 'Dhruv Supplier',
                'role_id' => $supplierRole->id,
                'role' => 'supplier',
                'organization_id' => $organizationOne->id,
                'password' => bcrypt('password'),
            ]
        );

        $supplierUserTwo = User::query()->updateOrCreate(
            ['email' => 'supplier.operations@gmail.com'],
            [
                'name' => 'Nimesh Supplier',
                'role_id' => $supplierRole->id,
                'role' => 'supplier',
                'organization_id' => $organizationTwo->id,
                'password' => bcrypt('password'),
            ]
        );

        $manufacturerBrand = Brand::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'name' => 'Manufacturer Core'],
            ['is_active' => true]
        );

        $manufacturerCategory = Category::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'name' => 'Finished Goods'],
            ['is_active' => true]
        );

        $manufacturerProduct = Product::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'name' => 'Refined Sunflower Oil 1L'],
            [
                'brand_id' => $manufacturerBrand->id,
                'category_id' => $manufacturerCategory->id,
                'sku' => 'MFG-OIL-1L',
                'price_cents' => 499,
                'sustainability_score' => 86.40,
                'carbon_kg_per_unit' => 1.1830,
                'is_active' => true,
                'description' => 'Cold pressed refined sunflower oil.',
                'variant_options' => ['size' => ['500ml', '1L', '2L']],
            ]
        );

        $organizationBrandOne = Brand::query()->updateOrCreate(
            ['organization_id' => $organizationOne->id, 'name' => 'Anakrani Select'],
            ['is_active' => true]
        );

        $organizationCategoryOne = Category::query()->updateOrCreate(
            ['organization_id' => $organizationOne->id, 'name' => 'Retail Oils'],
            ['is_active' => true]
        );

        $organizationProductOne = Product::query()->updateOrCreate(
            ['organization_id' => $organizationOne->id, 'name' => 'Anakrani Premium Oil'],
            [
                'brand_id' => $organizationBrandOne->id,
                'category_id' => $organizationCategoryOne->id,
                'sku' => 'ANA-PREM-001',
                'price_cents' => 599,
                'sustainability_score' => 79.10,
                'carbon_kg_per_unit' => 1.4300,
                'is_active' => true,
                'description' => 'Premium retail oil pack.',
                'variant_options' => ['pack' => ['Bottle', 'Can']],
            ]
        );

        $organizationBrandTwo = Brand::query()->updateOrCreate(
            ['organization_id' => $organizationTwo->id, 'name' => 'Dhnakrani Value'],
            ['is_active' => true]
        );

        $organizationCategoryTwo = Category::query()->updateOrCreate(
            ['organization_id' => $organizationTwo->id, 'name' => 'Bulk Oils'],
            ['is_active' => true]
        );

        $organizationProductTwo = Product::query()->updateOrCreate(
            ['organization_id' => $organizationTwo->id, 'name' => 'Dhnakrani Bulk Oil 5L'],
            [
                'brand_id' => $organizationBrandTwo->id,
                'category_id' => $organizationCategoryTwo->id,
                'sku' => 'DHN-BULK-005',
                'price_cents' => 2299,
                'sustainability_score' => 75.20,
                'carbon_kg_per_unit' => 3.0040,
                'is_active' => true,
                'description' => 'Bulk oil can for B2B customers.',
                'variant_options' => ['size' => ['5L', '15L']],
            ]
        );

        $rawMaterialOne = RawMaterial::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'name' => 'Sunflower Seeds'],
            [
                'sku' => 'RAW-SEED-001',
                'unit' => 'kg',
                'unit_cost_cents' => 180,
                'is_active' => true,
            ]
        );

        $rawMaterialTwo = RawMaterial::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'name' => 'Food Grade Bottle'],
            [
                'sku' => 'RAW-BOTTLE-001',
                'unit' => 'piece',
                'unit_cost_cents' => 35,
                'is_active' => true,
            ]
        );

        BomItem::query()->updateOrCreate(
            ['product_id' => $manufacturerProduct->id, 'raw_material_id' => $rawMaterialOne->id],
            [
                'alternative_raw_material_id' => $rawMaterialTwo->id,
                'quantity_required' => 0.850,
                'unit_cost_cents' => 180,
            ]
        );

        InventoryItem::query()->updateOrCreate(
            ['organization_id' => $manufacturerOrganization->id, 'product_id' => $manufacturerProduct->id],
            [
                'quantity_on_hand' => 2800,
                'reorder_threshold' => 500,
                'expiry_tracking_enabled' => true,
                'next_expiry_date' => now()->addMonths(10)->toDateString(),
            ]
        );

        $productionOrder = ProductionOrder::query()->updateOrCreate(
            ['order_number' => 'PO-MFG-2026-0001'],
            [
                'organization_id' => $manufacturerOrganization->id,
                'product_id' => $manufacturerProduct->id,
                'quantity_planned' => 5000,
                'quantity_completed' => 3420,
                'status' => 'in_production',
                'machine_utilization_percent' => 87.35,
                'defect_ratio_percent' => 1.40,
                'cost_per_unit_cents' => 312,
                'started_at' => now()->subDays(4),
                'completed_at' => null,
            ]
        );

        $supplierOne = Supplier::query()->updateOrCreate(
            ['organization_id' => $organizationOne->id, 'email' => 'dhruvcmdzone22@gmail.com'],
            [
                'user_id' => $supplierUserOne->id,
                'name' => 'Dhruv',
                'phone' => '+91-9090909090',
                'status' => 'active',
            ]
        );

        $supplierTwo = Supplier::query()->updateOrCreate(
            ['organization_id' => $organizationTwo->id, 'email' => 'supplier.operations@gmail.com'],
            [
                'user_id' => $supplierUserTwo->id,
                'name' => 'Nimesh',
                'phone' => '+91-8080808080',
                'status' => 'active',
            ]
        );

        $purchaseOrderOne = PurchaseOrder::query()->updateOrCreate(
            ['order_number' => 'PUR-ANA-2026-0001'],
            [
                'buyer_id' => $organizationOne->id,
                'supplier_id' => $supplierOne->id,
                'status' => 'approved',
                'total_amount_cents' => 980000,
                'approved_by' => $superAdmin->id,
                'blockchain_tx_hash' => '0x9f8a0e3f5cf3b0b4476ac4c8f301bc6fffe1d1019f3b12f5ac37ed5b8f91c641',
            ]
        );

        $purchaseOrderTwo = PurchaseOrder::query()->updateOrCreate(
            ['order_number' => 'PUR-DHN-2026-0001'],
            [
                'buyer_id' => $organizationTwo->id,
                'supplier_id' => $supplierTwo->id,
                'status' => 'in_production',
                'total_amount_cents' => 1280000,
                'approved_by' => $superAdmin->id,
                'blockchain_tx_hash' => '0x8b14bf267f68951d8a97259f399d2e22f8bcf1d3666cffeb579989f200e4d247',
            ]
        );

        PurchaseOrderItem::query()->updateOrCreate(
            ['purchase_order_id' => $purchaseOrderOne->id, 'product_id' => $organizationProductOne->id],
            [
                'quantity' => 1200,
                'price_cents' => 599,
            ]
        );

        PurchaseOrderItem::query()->updateOrCreate(
            ['purchase_order_id' => $purchaseOrderTwo->id, 'product_id' => $organizationProductTwo->id],
            [
                'quantity' => 850,
                'price_cents' => 2299,
            ]
        );

        DispatchOrder::query()->updateOrCreate(
            ['dispatch_number' => 'DSP-2026-001'],
            [
                'purchase_order_id' => $purchaseOrderOne->id,
                'status' => 'dispatched',
                'dispatched_at' => now()->subDay(),
            ]
        );

        Grn::query()->updateOrCreate(
            ['purchase_order_id' => $purchaseOrderOne->id],
            [
                'received_by' => $organizationAdminOne->id,
                'received_date' => now()->subHours(18)->toDateString(),
                'status' => 'received',
            ]
        );

        $invoiceOne = Invoice::query()->updateOrCreate(
            ['invoice_number' => 'INV-ANA-2026-001'],
            [
                'purchase_order_id' => $purchaseOrderOne->id,
                'external_reference' => 'QB-0001892',
                'amount_cents' => 718800,
                'tax_cents' => 129384,
                'due_date' => now()->addDays(21)->toDateString(),
                'status' => 'pending',
                'compliance_status' => 'compliant',
            ]
        );

        Payment::query()->updateOrCreate(
            ['invoice_id' => $invoiceOne->id, 'transaction_reference' => 'TXN-ANA-9001'],
            [
                'amount_paid_cents' => 250000,
                'payment_mode' => 'bank_transfer',
                'status' => 'partial',
            ]
        );

        Shipment::query()->updateOrCreate(
            ['tracking_number' => 'SHIP-ANA-5521'],
            [
                'purchase_order_id' => $purchaseOrderOne->id,
                'status' => 'in_transit',
                'shipped_date' => now()->subDay()->toDateString(),
                'delivered_date' => null,
            ]
        );

        $rfq = Rfq::query()->updateOrCreate(
            ['rfq_number' => 'RFQ-ANA-2026-001'],
            [
                'buyer_id' => $manufacturerOrganization->id,
                'supplier_id' => $organizationOne->id,
                'status' => 'open',
                'due_date' => now()->addDays(5)->toDateString(),
            ]
        );

        Quotation::query()->updateOrCreate(
            ['rfq_id' => $rfq->id],
            [
                'total_amount_cents' => 625000,
                'status' => 'submitted',
            ]
        );

        OrganizationRevenueSnapshot::query()->updateOrCreate(
            ['organization_id' => $organizationOne->id, 'recorded_at' => now()->startOfHour()],
            ['revenue_cents' => 5820000]
        );

        OrganizationRevenueSnapshot::query()->updateOrCreate(
            ['organization_id' => $organizationTwo->id, 'recorded_at' => now()->startOfHour()],
            ['revenue_cents' => 4310000]
        );
    }
}
