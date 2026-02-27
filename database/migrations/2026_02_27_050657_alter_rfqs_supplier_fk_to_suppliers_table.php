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
        Schema::table('rfqs', function (Blueprint $table): void {
            $table->dropForeign(['supplier_id']);
        });

        DB::table('rfqs')
            ->orderBy('id')
            ->get()
            ->each(function (object $rfq): void {
                $supplierId = DB::table('suppliers')
                    ->where('organization_id', $rfq->buyer_id)
                    ->orderBy('id')
                    ->value('id');

                if ($supplierId === null) {
                    return;
                }

                DB::table('rfqs')
                    ->where('id', $rfq->id)
                    ->update(['supplier_id' => $supplierId]);
            });

        Schema::table('rfqs', function (Blueprint $table): void {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table): void {
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('organizations')->cascadeOnDelete();
        });
    }
};
