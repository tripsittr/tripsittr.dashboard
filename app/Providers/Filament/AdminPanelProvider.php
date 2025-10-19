<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Admin;
use App\Filament\Clusters\Admin\Resources\UserResource;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Widgets\DashboardMusicForArtists;
use App\Http\Middleware\UpdateUserTeam;
use App\Models\Team;
use App\Support\Settings;
use App\Support\UserPreferences;
use Awcodes\Overlook\OverlookPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Maartenpaauw\Filament\Cashier\Stripe\BillingProvider;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function getUserTeamId(): ?string
    {
        $tenantId = Request::segment(1);

        return is_numeric($tenantId) ? $tenantId : null;
    }

    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('')
            // Dynamic navigation layout: user preference overrides global fallback
            ->topNavigation(
                function () {
                    $layout = UserPreferences::get('navigation_layout', Settings::get('navigation_layout', 'top'));

                    return $layout === 'top';
                }
            )
            ->databaseNotifications()
            ->tenantMenu(true)
            ->tenantBillingProvider(new BillingProvider('solo_artist'))
            ->tenantProfile(EditTeamProfile::class)
            ->tenantRegistration(RegisterTeam::class)
            ->requiresTenantSubscription(false)
            ->unsavedChangesAlerts()
            ->tenant(Team::class)
            ->registration(Register::class)
            ->plugins([
                OverlookPlugin::make()
                    ->sort(2)
                    ->excludes(
                        [
                            \App\Filament\Clusters\Admin\Resources\TeamResource::class,
                            UserResource::class,
                        ]
                    ),
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable(),
                EasyFooterPlugin::make()
                    ->withFooterPosition('footer')
                    ->withLinks(array_merge(
                        array_map(
                            fn ($link) => [
                                'title' => $link['title'] ?? 'Link',
                                'url' => $link['url'] ?? '#',
                            ],
                            Settings::get('footer_links', [
                                ['title' => 'Privacy Policy', 'url' => 'https://tripsittr.com/dashboard/privacy'],
                                ['title' => 'Terms of Service', 'url' => 'https://tripsittr.com/dashboard/terms'],
                            ])
                        ),
                        [
                            ['title' => 'Partners', 'url' => config('app.url').$this->getUserTeamId().'/partners'],
                        ]
                    ))
                    ->withLoadTime('This page loaded in')
                    ->withBorder(true),

            ])
            ->navigationGroups([
                NavigationGroup::make()->label('Music')->icon('heroicon-s-musical-note'),
                NavigationGroup::make()->label('Events')->icon('heroicon-s-calendar-days'),
                NavigationGroup::make()->label('Social Media')->icon('heroicon-s-chat-bubble-left-right'),
                NavigationGroup::make()->label('Analytics')->icon('heroicon-s-chart-pie'),
                NavigationGroup::make()->label('Administration'),
                NavigationGroup::make()->label('Extras')->icon('fas-square-plus'),
                NavigationGroup::make()->label('Content'),
            ])
            ->brandLogo(asset('/storage/Tripsittr Logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('/storage/Tripsittr Logo Record.png'))
            ->userMenuItems([
                MenuItem::make()
                    ->label('Knowledge')
                    ->url(fn (): string => '/'.$this->getUserTeamId().'/knowledge/knowledge')
                    ->icon('fas-book'),
                MenuItem::make()
                    ->label('Settings')
                    ->url(function (): string {
                        // Only build Settings URL if a tenant (team) context exists; otherwise return a safe placeholder
                        $tenantId = $this->getUserTeamId();
                        if (! $tenantId) {
                            return '#'; // hidden via visible() below; fallback prevents exception
                        }

                        return \App\Filament\Clusters\Settings\Settings::getUrl();
                    })
                    ->visible(fn () => \Filament\Facades\Filament::getTenant() !== null)
                    ->icon('heroicon-s-cog-6-tooth'),
                MenuItem::make()
                    ->label('Admin')
                    ->url(fn (): string => Admin::getUrl())
                    ->visible(fn (): bool => Auth::user()->type == 'Admin')
                    ->icon('fas-lock'),
            ])
            ->login()
            ->colors([
                'primary' => '#C75D5D',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DashboardMusicForArtists::class,
            ])
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
                UpdateUserTeam::class,
            ])
            // ->routeMiddleware([
            //     'team' => UpdateUserTeam::class,
            // ])
            ->authMiddleware([
                Authenticate::class,
                UpdateUserTeam::class,
            ]);
    }
}
