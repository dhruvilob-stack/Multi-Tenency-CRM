<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierInvitation>
 */
class SupplierInvitationFactory extends Factory
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
            'supplier_id' => \App\Models\Supplier::factory(),
            'invited_by' => null,
            'email' => $this->faker->safeEmail(),
            'token' => $this->faker->uuid(),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }
}
