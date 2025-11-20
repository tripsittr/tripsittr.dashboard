<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Clusters\Admin\Resources\TeamResource;
use App\Filament\Admin\Clusters\Admin\Resources\UserResource;
use App\Filament\Admin\Pages\Tenancy\EditTeamProfile;
use App\Filament\Artists\Clusters\Extras\Pages\ExtractAudio;
use App\Filament\Artists\Clusters\Playlists\Playlists;
use App\Filament\Index\Pages\Dashboard;
use App\Filament\Index\Pages\Partners;
use App\Filament\Index\Widgets\DashboardMusicForArtists;
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
use Illuminate\Support\Facades\Request;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Maartenpaauw\Filament\Cashier\Stripe\BillingProvider;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class ArtistsPanelProvider extends PanelProvider
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
            ->id('artists')
            ->path('artists')
            ->login()
            ->topNavigation(
                function () {
                    $layout = UserPreferences::get('navigation_layout', Settings::get('navigation_layout', 'top'));

                    return $layout === 'top';
                }
            )
            ->databaseNotifications()
            ->tenantMenu(true)
            ->tenantMenu(true)
            ->tenantBillingProvider(new BillingProvider('solo_artist'))
            ->tenantProfile(EditTeamProfile::class)
            ->requiresTenantSubscription(false)
            ->tenant(Team::class)
            ->tenantRegistration(\App\Filament\Artists\Pages\Tenancy\RegisterTeam::class)
            ->registration(\App\Filament\Artists\Pages\Register::class)
            ->plugins([
                OverlookPlugin::make()
                    ->sort(2)
                    ->excludes(
                        [
                            TeamResource::class,
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

                // ApexCharts plugin for Filament widgets (charts used in Analytics)
                FilamentApexChartsPlugin::make(),

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
                    ->label('Messages')
                    ->icon('heroicon-s-chat-bubble-left-right')
                    ->visible(fn () => \Filament\Facades\Filament::getTenant() !== null)
                    ->url(fn () => ($tenant = \Filament\Facades\Filament::getTenant()) ? url('artists/'.$tenant->id.'/direct-message-threads') : url('artists')),
                MenuItem::make()
                    ->label('Knowledge')
                    ->visible(fn () => \Filament\Facades\Filament::getTenant() !== null)
                    ->url(fn (): string => ($tenant = \Filament\Facades\Filament::getTenant()) ? config('app.url').'artists/'.$tenant->id.'/knowledge' : config('app.url').'artists')
                    ->icon('fas-book'),
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn (): string => ($tenant = \Filament\Facades\Filament::getTenant()) ? config('app.url').'artists/'.$tenant->id.'/settings' : config('app.url').'artists')
                    ->visible(fn () => \Filament\Facades\Filament::getTenant() !== null)
                    ->icon('heroicon-s-cog-6-tooth'),
            ])
            ->colors([
                'primary' => '#C75D5D',
            ])
            ->discoverResources(in: app_path('Filament/Artists/Resources'), for: 'App\\Filament\\Artists\\Resources')
            ->resources([
                \App\Filament\Resources\DirectMessageThreadResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Artists/Pages'), for: 'App\\Filament\\Artists\\Pages')
            ->discoverClusters(in: app_path('Filament/Artists/Clusters'), for: 'App\\Filament\\Artists\\Clusters')
            ->pages([
                Dashboard::class,
                ExtractAudio::class,
                Partners::class,
                Playlists::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Index/Widgets'), for: 'App\\Filament\\Index\\Widgets')
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
