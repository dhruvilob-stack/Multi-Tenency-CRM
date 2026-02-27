<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\TwoLetterUiAvatarsProvider;
use App\Filament\Organization\Widgets\OrganizationPerformanceStats;
use App\Filament\Organization\Widgets\PurchaseOrdersOverview;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganizationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('organization')
            ->path('organization')
            ->login()
            ->brandName('Organization Portal')
            ->profile(EditProfile::class, isSimple: false)
            ->spa(hasPrefetching: true)
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->defaultAvatarProvider(TwoLetterUiAvatarsProvider::class)
            ->colors([
                'primary' => Color::Emerald,
                'info' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Organization/Resources'), for: 'App\Filament\Organization\Resources')
            ->discoverPages(in: app_path('Filament/Organization/Pages'), for: 'App\Filament\Organization\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Organization/Widgets'), for: 'App\Filament\Organization\Widgets')
            ->widgets([
                OrganizationPerformanceStats::class,
                PurchaseOrdersOverview::class,
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.auth.google-login-button', ['panel' => 'organization'])
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.partials.center-global-search')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn () => view('filament.partials.topbar-language-switcher')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.partials.notification-row-highlight')
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
