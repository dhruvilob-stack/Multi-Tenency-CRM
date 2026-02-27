<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_admin_can_accept_invitation(): void
    {
        $role = Role::query()->create([
            'name' => 'organization_admin',
            'permissions' => [],
        ]);

        $organization = Organization::factory()->create([
            'type' => 'buyer',
        ]);

        $invitation = UserInvitation::query()->create([
            'name' => 'Organization User',
            'email' => 'org-user@example.com',
            'role_id' => $role->id,
            'role' => 'organization_admin',
            'organization_id' => $organization->id,
            'permissions' => ['organization_dashboard.view'],
            'token' => 'org-token',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post(route('user.invitation.accept', $invitation->token), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'org-user@example.com',
            'role' => 'organization_admin',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_supplier_invitation_creates_supplier_link(): void
    {
        $role = Role::query()->create([
            'name' => 'supplier',
            'permissions' => [],
        ]);

        $organization = Organization::factory()->create([
            'type' => 'buyer',
        ]);

        $invitation = UserInvitation::query()->create([
            'name' => 'Supplier User',
            'email' => 'supplier-user@example.com',
            'role_id' => $role->id,
            'role' => 'supplier',
            'organization_id' => $organization->id,
            'permissions' => ['supplier_dashboard.view'],
            'token' => 'supplier-token',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post(route('user.invitation.accept', $invitation->token), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('suppliers', [
            'organization_id' => $organization->id,
            'email' => 'supplier-user@example.com',
            'status' => 'active',
        ]);

        $this->assertNotNull(Supplier::query()->where('email', 'supplier-user@example.com')->first()?->user_id);
    }
}
