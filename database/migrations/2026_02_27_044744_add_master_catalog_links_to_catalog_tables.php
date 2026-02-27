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
        Schema::table('brands', function (Blueprint $table): void {
            $table
                ->foreignId('master_brand_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('brands')
                ->nullOnDelete();

            $table->unique(['organization_id', 'master_brand_id']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table
                ->foreignId('master_category_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('categories')
                ->nullOnDelete();

            $table->unique(['organization_id', 'master_category_id']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table
                ->foreignId('master_product_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('products')
                ->nullOnDelete();

            $table->unique(['organization_id', 'master_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropUnique(['organization_id', 'master_product_id']);
            $table->dropConstrainedForeignId('master_product_id');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique(['organization_id', 'master_category_id']);
            $table->dropConstrainedForeignId('master_category_id');
        });

        Schema::table('brands', function (Blueprint $table): void {
            $table->dropUnique(['organization_id', 'master_brand_id']);
            $table->dropConstrainedForeignId('master_brand_id');
        });
    }
};
