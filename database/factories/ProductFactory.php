<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'brand_id' => \App\Models\Brand::factory(),
            'category_id' => \App\Models\Category::factory(),
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->bothify('SKU-####')),
            'price_cents' => $this->faker->numberBetween(1000, 50000),
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }
}
