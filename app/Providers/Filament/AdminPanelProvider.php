<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Admin;
use App\Filament\Clusters\Admin\Resources\UserResource;
use App\Filament\Clusters\Knowledge\Resources\KnowledgeResource;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ListTeamMembers;
use App\Filament\Pages\Partners;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Widgets\DashboardMusicForArtists;
use App\Http\Middleware\UpdateUserTeam;
use App\Models\Team;
use Awcodes\Overlook\OverlookPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Maartenpaauw\Filament\Cashier\Stripe\BillingProvider;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Request;
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
                            UserResource::class,
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
                        ['title' => 'Partners', 'url' => config('app.url') . $this->getUserTeamId() . '/partners'],
                    ])
                    ->withLoadTime('This page loaded in')
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
                    ->label('Administration'),
                NavigationGroup::make()
                    ->label('Extras')
                    ->icon('fas-square-plus'),
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
                    ->label('Admin')
                    ->url(fn(): string => Admin::getUrl())
                    ->visible(fn(): bool => Auth::user()->type == 'Admin')
                    ->icon('fas-lock'),
            ])
            ->tenantMenuItems([
                'register' => MenuItem::make()->label('Register New Team')
                    ->visible(fn(): bool => Auth::user()->type == 'Admin'),
                'profile' => MenuItem::make()->label('Edit Team Profile'),
                'members' => MenuItem::make()->label('Manage Members')->url(fn(): string => ListTeamMembers::getUrl()),
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
