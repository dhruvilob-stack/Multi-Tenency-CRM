<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptSupplierInvitationRequest;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Support\WorkflowNotifier;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SupplierInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = SupplierInvitation::query()
            ->with('supplier')
            ->where('token', $token)
            ->firstOrFail();

        $isExpired = $invitation->expires_at->isPast();

        return view('suppliers.accept-invitation', [
            'invitation' => $invitation,
            'isExpired' => $isExpired,
        ]);
    }

    public function accept(AcceptSupplierInvitationRequest $request, string $token): RedirectResponse
    {
        $invitation = SupplierInvitation::query()
            ->with('supplier')
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->accepted_at || $invitation->expires_at->isPast()) {
            return redirect()->route('supplier.invitation.show', $token)
                ->withErrors(['email' => 'This invitation has expired or was already used.']);
        }

        $user = User::query()->where('email', $invitation->email)->first();

        $supplierRoleId = Role::query()->where('name', 'supplier')->value('id');

        if (! $user) {
            $user = User::query()->create([
                'name' => $invitation->supplier->name,
                'email' => $invitation->email,
                'password' => $request->validated('password'),
                'email_verified_at' => now(),
                'role_id' => $supplierRoleId,
                'role' => 'supplier',
                'organization_id' => $invitation->organization_id,
                'is_active' => true,
            ]);
        } else {
            $user->forceFill([
                'password' => $request->validated('password'),
                'role_id' => $supplierRoleId,
                'role' => 'supplier',
                'organization_id' => $invitation->organization_id,
                'is_active' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        $invitation->supplier->forceFill([
            'user_id' => $user->id,
            'status' => 'active',
        ])->save();

        $invitation->forceFill(['accepted_at' => now()])->save();

        AuditLog::query()->create([
            'organization_id' => $invitation->organization_id,
            'user_id' => $user->id,
            'action' => 'supplier.invitation.accepted',
            'auditable_type' => SupplierInvitation::class,
            'auditable_id' => $invitation->id,
            'metadata' => ['email' => $invitation->email],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        WorkflowNotifier::notifySuperAdmins(
            'Supplier joined organization',
            sprintf('%s joined %s.', $user->email, (string) $invitation->organization?->name),
            '/manufacturer/suppliers',
            'catalog'
        );

        Auth::login($user);

        return redirect()->to(Filament::getPanel('super-admin')->getUrl());
    }
}
