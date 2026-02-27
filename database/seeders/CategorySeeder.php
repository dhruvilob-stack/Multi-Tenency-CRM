<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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

        Category::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'Electronics'],
            ['is_active' => true]
        );
        Category::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'Packaging'],
            ['is_active' => true]
        );
        Category::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'Hardware'],
            ['is_active' => true]
        );
    }
}
