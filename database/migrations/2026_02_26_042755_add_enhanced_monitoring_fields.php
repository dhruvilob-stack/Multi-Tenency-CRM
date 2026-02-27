<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->decimal('esg_score', 5, 2)->nullable()->after('currency_code');
            $table->string('tenant_code')->nullable()->unique()->after('slug');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('sustainability_score', 5, 2)->nullable()->after('price_cents');
            $table->decimal('carbon_kg_per_unit', 10, 4)->nullable()->after('sustainability_score');
            $table->json('variant_options')->nullable()->after('description');
        });

        Schema::table('bom_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('unit_cost_cents')->default(0)->after('quantity_required');
            $table->foreignId('alternative_raw_material_id')
                ->nullable()
                ->after('raw_material_id')
                ->constrained('raw_materials')
                ->nullOnDelete();
        });

        Schema::table('production_orders', function (Blueprint $table): void {
            $table->decimal('machine_utilization_percent', 5, 2)->nullable()->after('status');
            $table->decimal('defect_ratio_percent', 5, 2)->nullable()->after('machine_utilization_percent');
            $table->unsignedBigInteger('cost_per_unit_cents')->default(0)->after('defect_ratio_percent');
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->boolean('expiry_tracking_enabled')->default(false)->after('reorder_threshold');
            $table->date('next_expiry_date')->nullable()->after('expiry_tracking_enabled');
        });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->string('blockchain_tx_hash')->nullable()->after('approved_by');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->string('external_reference')->nullable()->after('invoice_number');
            $table->string('compliance_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropColumn(['external_reference', 'compliance_status']);
        });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->dropColumn(['blockchain_tx_hash']);
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->dropColumn(['expiry_tracking_enabled', 'next_expiry_date']);
        });

        Schema::table('production_orders', function (Blueprint $table): void {
            $table->dropColumn(['machine_utilization_percent', 'defect_ratio_percent', 'cost_per_unit_cents']);
        });

        Schema::table('bom_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('alternative_raw_material_id');
            $table->dropColumn(['unit_cost_cents']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['sustainability_score', 'carbon_kg_per_unit', 'variant_options']);
        });

        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropUnique('organizations_tenant_code_unique');
            $table->dropColumn(['esg_score', 'tenant_code']);
        });
    }
};
