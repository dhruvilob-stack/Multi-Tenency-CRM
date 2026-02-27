<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_accept_invitation_and_set_password(): void
    {
        $organization = Organization::factory()->create();
        $supplier = Supplier::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'supplier@example.com',
            'status' => 'invited',
        ]);

        $invitation = SupplierInvitation::factory()->create([
            'organization_id' => $organization->id,
            'supplier_id' => $supplier->id,
            'email' => 'supplier@example.com',
            'token' => 'test-token',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post(route('supplier.invitation.accept', $invitation->token), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'supplier@example.com',
            'role' => 'supplier',
            'organization_id' => $organization->id,
        ]);

        $this->assertDatabaseHas('supplier_invitations', [
            'id' => $invitation->id,
        ]);

        $supplier->refresh();
        $this->assertSame('active', $supplier->status);
        $this->assertNotNull($supplier->user_id);
        $this->assertNotNull(User::find($supplier->user_id));
    }
}
