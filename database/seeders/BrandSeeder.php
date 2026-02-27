<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
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

        Brand::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'NovaLine'],
            ['is_active' => true]
        );
        Brand::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'ForgeWorks'],
            ['is_active' => true]
        );
    }
}
