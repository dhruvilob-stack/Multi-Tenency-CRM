<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationRevenueSnapshot>
 */
class OrganizationRevenueSnapshotFactory extends Factory
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
            'revenue_cents' => $this->faker->numberBetween(10000, 500000),
            'recorded_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
