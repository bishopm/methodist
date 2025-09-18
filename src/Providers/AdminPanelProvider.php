<?php

namespace Bishopm\Methodist\Providers;

use Alareqi\FilamentPwa\FilamentPwaPlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Bishopm\Methodist\Filament\Pages\Dashboard;
use Bishopm\Methodist\Filament\Widgets\Map;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Society;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public $circuit;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Zinc
            ])
            ->brandLogo(asset('methodist/images/mcsa.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('methodist/images/favicon.png'))
            ->discoverResources(in: base_path('vendor/bishopm/methodist/src/Filament/Resources'), for: 'Bishopm\\Methodist\\Filament\\Resources')
            ->discoverPages(in: base_path('vendor/bishopm/methodist/src/Filament/Pages'), for: 'Bishopm\\Methodist\\Filament\\Pages')
            ->discoverClusters(in: base_path('vendor/bishopm/methodist/src/Filament/Clusters'), for: 'Bishopm\\Methodist\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->plugins([
                FilamentPwaPlugin::make()
                    ->name('MCSA')
                    ->shortName('MCSA')
                    ->description('MCSA PWA')
                    ->themeColor('#fe0002')
                    ->backgroundColor('#ffffff')
                    ->standalone()
                    ->language('en')
                    ->ltr()
                    ->installation(true,2000,5000,false)
                    ->addShortcut('Dashboard', '/admin', 'Main dashboard')
                    ->icons('methodist/images/icons', [72, 96, 128, 144, 152, 192, 384, 512])
                    ->serviceWorker('my-app-v1.0.0', '/offline'),
                FilamentSpatieRolesPermissionsPlugin::make(),
                FilamentSettingsPlugin::make()
                    ->pages([
                    ])
            ])
            ->widgets([
                Map::class
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
                MenuItem::make()
                    ->url(function (){
                        $user=auth()->user();
                        if ($user->circuits){
                            $this->circuit=$user->circuits[0];
                        } else if ($user->societies){
                            $this->circuit=Society::find($user->societies[0])->circuit_id;
                        } else {
                            $this->circuit="";
                        }
                        return '/admin/circuits/' . $this->circuit;
                    })
                    ->icon('heroicon-o-user-group')
                    ->label(function (){
                        if ($this->circuit==""){
                            return "My circuits";
                        } else {
                            $circ=Circuit::find($this->circuit);
                            return $circ->circuit;
                        }
                    }),
                MenuItem::make()
                    ->label('Settings')
                    ->url('/admin/settings')
                    ->visible(fn (): bool => auth()->user()->isSuperAdmin())
                    ->icon('heroicon-o-cog-8-tooth'),      
                MenuItem::make()
                    ->label('Website')
                    ->url('/')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-globe-alt'),
            ]);
    }
}
