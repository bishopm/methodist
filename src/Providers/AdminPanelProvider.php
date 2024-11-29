<?php

namespace Bishopm\Methodist\Providers;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\Filament\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Teal
            ])
            ->discoverResources(in: base_path('vendor/bishopm/methodist/src/Filament/Resources'), for: 'Bishopm\\Methodist\\Filament\\Resources')
            ->discoverPages(in: base_path('vendor/bishopm/methodist/src/Filament/Pages'), for: 'Bishopm\\Methodist\\Filament\\Pages')
            ->discoverClusters(in: base_path('vendor/bishopm/methodist/src/Filament/Clusters'), for: 'Bishopm\\Methodist\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
                \Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin::make()
                    ->pages([
                    ])
            ])
            ->widgets([
            ])
            ->sidebarCollapsibleOnDesktop()
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
            ->userMenuItems([
            ]);
    }
}
