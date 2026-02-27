<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $request->session()->put('auth.panel', 'super-admin');

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::query()->where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return $this->redirectToLogin()
                ->withErrors(['email' => 'No account is registered for this Google email.']);
        }

        if (! $user->is_active) {
            return $this->redirectToLogin()
                ->withErrors(['email' => 'This account is inactive.']);
        }

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $panel = Filament::getPanel('super-admin');

        if (! $user->canAccessPanel($panel)) {
            return $this->redirectToLogin()
                ->withErrors(['email' => 'Your account is not authorized for the Master CRM panel.']);
        }

        Auth::login($user, true);

        return redirect()->to($panel->getUrl());
    }

    private function redirectToLogin(): RedirectResponse
    {
        return redirect()->to(Filament::getPanel('super-admin')->getLoginUrl());
    }
}
