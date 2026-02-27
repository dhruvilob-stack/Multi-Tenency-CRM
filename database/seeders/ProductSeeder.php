<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::query()->first();

        if (! $organization) {
            return;
        }

        $brand = Brand::query()->where('organization_id', $organization->id)->first();
        $category = Category::query()->where('organization_id', $organization->id)->first();

        if (! $brand || ! $category) {
            return;
        }

        Product::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'sku' => 'SKU-1001',
            ],
            [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Precision Gearbox',
                'price_cents' => 125000,
                'is_active' => true,
                'description' => 'High torque industrial gearbox.',
            ]
        );

        Product::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'sku' => 'SKU-1002',
            ],
            [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Industrial Sensor Pack',
                'price_cents' => 45000,
                'is_active' => true,
                'description' => 'Multi-sensor pack for production monitoring.',
            ]
        );
    }
}
