<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });

        DB::table('purchase_orders')
            ->orderBy('id')
            ->get()
            ->each(function (object $purchaseOrder): void {
                $supplierId = DB::table('suppliers')
                    ->where('organization_id', $purchaseOrder->buyer_id)
                    ->orderBy('id')
                    ->value('id');

                if ($supplierId === null) {
                    return;
                }

                DB::table('purchase_orders')
                    ->where('id', $purchaseOrder->id)
                    ->update(['supplier_id' => $supplierId]);
            });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('organizations')->cascadeOnDelete();
        });
    }
};
