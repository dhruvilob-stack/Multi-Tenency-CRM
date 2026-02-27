<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\TwoLetterUiAvatarsProvider;
use App\Filament\Organization\Widgets\OrganizationPerformanceStats;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\ManufacturerDashboard;
use App\Filament\Pages\OrganizationDashboard;
use App\Filament\Pages\SupplierDashboard;
use App\Filament\Pages\Welcome;
use App\Filament\Supplier\Widgets\SupplierPerformanceStats;
use App\Filament\Widgets\PurchaseOrdersOverview;
use App\Filament\Widgets\SuperAdminPerformanceStats;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()

            ->id('super-admin')
            ->path('')
            ->login()
            ->brandName(function (): string {
                $user = auth()->user();

                if (! $user) {
                    return 'CRM Portal';
                }

                if ($user->isSuperAdmin()) {
                    return 'Master CRM Panel';
                }

                if ($user->isManufacturer()) {
                    return 'Manufacturer Panel';
                }

                if ($user->isOrganizationAdmin()) {
                    return 'Organization Panel';
                }

                if ($user->isSupplier()) {
                    return 'Supplier Panel';
                }

                return 'CRM Portal';
            })
            ->profile(EditProfile::class, isSimple: false)
            ->spa(hasPrefetching: true)
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->defaultAvatarProvider(TwoLetterUiAvatarsProvider::class)
            ->colors([
                'primary' => Color::Amber,
                'info' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverResources(in: app_path('Filament/Organization/Resources'), for: 'App\Filament\Organization\Resources')
            ->discoverResources(in: app_path('Filament/Supplier/Resources'), for: 'App\Filament\Supplier\Resources')
            ->pages([
                Welcome::class,
                ManufacturerDashboard::class,
                OrganizationDashboard::class,
                SupplierDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->discoverWidgets(in: app_path('Filament/Organization/Widgets'), for: 'App\Filament\Organization\Widgets')
            ->discoverWidgets(in: app_path('Filament/Supplier/Widgets'), for: 'App\Filament\Supplier\Widgets')
            ->widgets([
                SuperAdminPerformanceStats::class,
                PurchaseOrdersOverview::class,
                OrganizationPerformanceStats::class,
                SupplierPerformanceStats::class,
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.auth.google-login-button', ['panel' => 'super-admin'])
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
            ])
            ->navigationGroups([
                'Master CRM Panel',
                'Manufacturer',
                'Organization',
                'Supplier',
            ]);
    }
}
