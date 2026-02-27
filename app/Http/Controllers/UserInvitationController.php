<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptUserInvitationRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserInvitation;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = UserInvitation::query()
            ->with(['organization', 'roleModel'])
            ->where('token', $token)
            ->firstOrFail();

        $isExpired = $invitation->expires_at->isPast();

        return view('invitations.accept-user', [
            'invitation' => $invitation,
            'isExpired' => $isExpired,
        ]);
    }

    public function accept(AcceptUserInvitationRequest $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::query()
            ->with(['organization', 'roleModel'])
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->accepted_at || $invitation->expires_at->isPast()) {
            return redirect()->route('user.invitation.show', $token)
                ->withErrors(['email' => 'This invitation has expired or was already used.']);
        }

        $roleId = $invitation->role_id ?? $invitation->roleModel?->id;
        $roleName = (string) ($invitation->roleModel?->name ?? $invitation->role);

        $user = User::query()->where('email', $invitation->email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => $request->validated('password'),
                'email_verified_at' => now(),
                'role_id' => $roleId,
                'role' => $roleName,
                'organization_id' => $invitation->organization_id,
                'permissions' => $invitation->permissions,
                'is_active' => true,
            ]);
        } else {
            $user->forceFill([
                'name' => $invitation->name,
                'password' => $request->validated('password'),
                'email_verified_at' => $user->email_verified_at ?? now(),
                'role_id' => $roleId,
                'role' => $roleName,
                'organization_id' => $invitation->organization_id,
                'permissions' => $invitation->permissions,
                'is_active' => true,
            ])->save();
        }

        if ($roleName === 'supplier' && filled($invitation->organization_id)) {
            Supplier::query()->updateOrCreate(
                [
                    'organization_id' => (int) $invitation->organization_id,
                    'email' => $invitation->email,
                ],
                [
                    'user_id' => $user->id,
                    'name' => $invitation->name,
                    'status' => 'active',
                ]
            );
        }

        $invitation->forceFill(['accepted_at' => now()])->save();

        $panel = Filament::getPanel('super-admin');

        if ($user->canAccessPanel($panel)) {
            Auth::login($user);

            return redirect()->to($panel->getUrl());
        }

        return redirect()->to($panel->getLoginUrl())
            ->with('status', 'Your account is activated. Please contact the admin for panel access.');
    }
}
