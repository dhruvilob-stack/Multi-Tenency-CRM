<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Support\WorkflowNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WorkflowNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_notify_manufacturer_admins_queries_manufacturer_organization_users(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'super_admin',
            'permissions' => [],
        ]);

        $organizationAdminRole = Role::query()->create([
            'name' => 'organization_admin',
            'permissions' => [],
        ]);

        $manufacturer = Organization::factory()->create([
            'type' => 'manufacturer',
        ]);

        $buyer = Organization::factory()->create([
            'type' => 'buyer',
        ]);

        User::factory()->create([
            'role_id' => $superAdminRole->id,
            'role' => 'super_admin',
            'organization_id' => null,
        ]);

        User::factory()->create([
            'role_id' => $organizationAdminRole->id,
            'role' => 'organization_admin',
            'organization_id' => $manufacturer->id,
        ]);

        User::factory()->create([
            'role_id' => $organizationAdminRole->id,
            'role' => 'organization_admin',
            'organization_id' => $buyer->id,
        ]);

        $bindings = [];

        DB::listen(function ($query) use (&$bindings): void {
            $bindings[] = $query->bindings;
        });

        WorkflowNotifier::notifyManufacturerAdmins('Test', 'Body');

        $flatBindings = array_merge(...$bindings);

        $this->assertContains('manufacturer', $flatBindings);
    }
}
