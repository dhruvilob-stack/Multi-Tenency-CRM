<?php

namespace Tests\Feature;

use App\Filament\Pages\ManufacturerDashboard;
use App\Filament\Pages\Welcome;
use App\Models\Role;
use App\Models\User;
use App\Support\DashboardNavigationItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNavigationSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_manufacturer_dashboard_shows_only_permitted_tabs(): void
    {
        $role = Role::query()->create([
            'name' => 'manufacturer',
            'permissions' => [],
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'role' => 'manufacturer',
            'is_active' => true,
            'permissions' => [
                'manufacturer_dashboard.view',
                'products.view',
                'products.manage',
                'orders.view',
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/manufacturer-dashboard');

        $response->assertOk();
        $response->assertSee('Products');
        $response->assertSee('RFQs');
        $response->assertDontSee('Suppliers');
    }

    public function test_welcome_page_does_not_render_dashboard_child_tabs(): void
    {
        $role = Role::query()->create([
            'name' => 'super_admin',
            'permissions' => [],
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'role' => 'super_admin',
            'is_active' => true,
            'permissions' => [
                'welcome.view',
                'products.view',
                'orders.view',
                'purchase_orders.view',
                'org_operations.view',
                'org_finance.view',
                'supplier_finance.view',
                'supplier_logistics.view',
            ],
        ]);

        $this->actingAs($user);

        $navigationItems = DashboardNavigationItems::forPage(Welcome::class);

        $this->assertSame([], $navigationItems);
    }

    public function test_dashboard_tabs_reflect_user_permission_changes_immediately(): void
    {
        $role = Role::query()->create([
            'name' => 'manufacturer',
            'permissions' => [],
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'role' => 'manufacturer',
            'is_active' => true,
            'permissions' => [
                'manufacturer_dashboard.view',
                'quality_reports.view',
            ],
        ]);

        $this->actingAs($user);

        $initialLabels = array_map(
            fn ($item): string => $item->getLabel(),
            DashboardNavigationItems::forPage(ManufacturerDashboard::class),
        );

        $this->assertContains('Quality Reports', $initialLabels);

        $user->update([
            'permissions' => [
                'manufacturer_dashboard.view',
            ],
        ]);

        $this->actingAs($user->fresh());

        $updatedLabels = array_map(
            fn ($item): string => $item->getLabel(),
            DashboardNavigationItems::forPage(ManufacturerDashboard::class),
        );

        $this->assertNotContains('Quality Reports', $updatedLabels);
    }
}
