<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
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
            'user_id' => \App\Models\User::factory(),
            'action' => $this->faker->word(),
            'auditable_type' => \App\Models\Organization::class,
            'auditable_id' => $this->faker->randomNumber(),
            'metadata' => ['note' => $this->faker->sentence()],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => now(),
        ];
    }
}
