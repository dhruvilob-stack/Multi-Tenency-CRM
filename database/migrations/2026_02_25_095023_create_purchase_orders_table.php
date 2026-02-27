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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('total_amount_cents')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
