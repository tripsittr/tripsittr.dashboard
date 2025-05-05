<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Knowledge;
use App\Filament\Clusters\Knowledge\Resources\KnowledgeResource;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Partners;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Widgets\AlbumSongCountStats;
use App\Filament\Widgets\CollapsibleContainerWidget;
use App\Filament\Widgets\DashboardCalendar;
use App\Filament\Pages\InstagramAnalytics;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\DashboardMusicForArtists;
use App\Models\Album;
use App\Models\Team;
use Awcodes\Overlook\OverlookPlugin;
use Awcodes\Recently\RecentlyPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Maartenpaauw\Filament\Cashier\Stripe\BillingProvider;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\View\LegacyComponents\Widget;
use Illuminate\Validation\Rules\Exists;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AdminPanelProvider extends PanelProvider {
    public function panel(Panel $panel): Panel {
        $user = Auth::user();
        $teamType = null;

        if ($user) {
            $teamType = Filament::getTenant()->type;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->topNavigation(true)
            ->databaseNotifications()
            ->tenantBillingProvider(new BillingProvider('solo_artist'))
            ->tenantProfile(EditTeamProfile::class)
            ->tenantRegistration(RegisterTeam::class)
            ->unsavedChangesAlerts()
            ->tenant(Team::class)
            // ->registration()
            ->plugins([
                OverlookPlugin::make()
                    ->sort(2)
                    ->excludes(
                        [
                            \App\Filament\Resources\TeamResource::class,
                            \App\Filament\Resources\UserResource::class,
                        ]
                    ),
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable(),
                EasyFooterPlugin::make()
                    ->withFooterPosition('footer')
                    ->withLinks([
                        ['title' => 'Privacy Policy', 'url' => 'https://tripsittr.com/privacy-policy'],
                        ['title' => 'Terms of Service', 'url' => 'https://tripsittr.com/terms-of-service'],
                    ])
                    ->withLoadTime('This page loaded in')
                    ->withFooterPosition('sidebar.footer')
                    ->withBorder(true),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Music')
                    ->icon('heroicon-s-musical-note'),
                NavigationGroup::make()
                    ->label('Events')
                    ->icon('heroicon-s-calendar-days'),
                NavigationGroup::make()
                    ->label('Social Media')
                    ->icon('heroicon-s-chat-bubble-left-right'),
                NavigationGroup::make()
                    ->label('Analytics')
                    ->icon('heroicon-s-chart-pie'),
                NavigationGroup::make()
                    ->label('Partners') 
                    ->icon('heroicon-s-globe-alt'),
                NavigationGroup::make()
                    ->label('Administration')
                    ->icon('heroicon-s-lock-closed')
            ])
            ->brandLogo(asset('/storage/Tripsittr Logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('/storage/Tripsittr Logo Record.png'))
            ->userMenuItems([
                MenuItem::make()
                    ->label('Knowledge')
                    ->url(fn(): string => KnowledgeResource::getUrl())
                    ->icon('fas-book'),
                MenuItem::make()
                    ->label('Users')
                    ->url(fn(): string => UserResource::getUrl())
                    ->visible(fn(): bool => Auth::user()->type == 'Admin')
                    ->icon('heroicon-s-users'),
            ])
            ->tenantMenuItems([
                'register' => MenuItem::make()->label('Register New Team')
                    ->visible(fn(): bool => Auth::user()->type == 'Admin'),
                'profile' => MenuItem::make()->label('Edit Team Profile'),
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
