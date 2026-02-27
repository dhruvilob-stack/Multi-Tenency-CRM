<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\TwoLetterUiAvatarsProvider;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Supplier\Widgets\PurchaseOrdersOverview;
use App\Filament\Supplier\Widgets\SupplierPerformanceStats;
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

class SupplierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supplier')
            ->path('supplier')
            ->login()
            ->brandName('Supplier Hub')
            ->profile(EditProfile::class, isSimple: false)
            ->spa(hasPrefetching: true)
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->defaultAvatarProvider(TwoLetterUiAvatarsProvider::class)
            ->colors([
                'primary' => Color::Indigo,
                'info' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Supplier/Resources'), for: 'App\Filament\Supplier\Resources')
            ->discoverPages(in: app_path('Filament/Supplier/Pages'), for: 'App\Filament\Supplier\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Supplier/Widgets'), for: 'App\Filament\Supplier\Widgets')
            ->widgets([
                SupplierPerformanceStats::class,
                PurchaseOrdersOverview::class,
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.auth.google-login-button', ['panel' => 'supplier'])
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
