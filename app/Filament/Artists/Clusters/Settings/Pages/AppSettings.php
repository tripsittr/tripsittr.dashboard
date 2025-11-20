<?php

namespace App\Filament\Artists\Clusters\Settings\Pages;

use App\Support\Settings;
use App\Support\UserPreferences;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AppSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.settings.app-settings';

    protected static ?string $navigationIcon = 'heroicon-s-cog';

    protected static ?string $navigationLabel = 'App Settings';

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Settings\Settings::class;

    public array $data = [];

    public bool $isAdminFlag = false;

    public function mount(): void
    {
        // All authenticated users may access the page; non-admins get a trimmed form.
        $this->isAdminFlag = $this->isAdmin();
        $isAdmin = $this->isAdminFlag;

        $state = [
            'navigation_layout' => UserPreferences::get('navigation_layout', Settings::get('navigation_layout', 'top')),
        ];

        if ($isAdmin) {
            $state += [
                'footer_links' => Settings::get('footer_links', [
                    ['title' => 'Privacy Policy', 'url' => 'https://tripsittr.com/dashboard/privacy'],
                    ['title' => 'Terms of Service', 'url' => 'https://tripsittr.com/dashboard/terms'],
                ]),
                'maintenance_mode' => Settings::get('maintenance_mode', false),
                'feature_flags' => Settings::get('feature_flags', [
                    'beta_music_insights' => false,
                    'experimental_scheduler' => false,
                    'ai_summarization' => false,
                ]),
                'branding' => Settings::get('branding', [
                    'marketing_site_url' => config('app.url'),
                    'support_email' => 'support@tripsittr.com',
                    'support_portal_url' => 'https://help.tripsittr.com',
                ]),
                'rate_limits' => Settings::get('rate_limits', [
                    'api_per_minute' => 120,
                    'ingest_per_hour' => 500,
                ]),
                'analytics' => Settings::get('analytics', [
                    'enable_internal_metrics' => true,
                    'enable_error_queue' => true,
                    'mask_user_pii' => true,
                ]),
            ];
        }

        $this->form->fill($state);
    }

    protected function getFormSchema(): array
    {
        $schema = [
            Section::make('Layout')
                ->description('Personal layout preference for navigation.')
                ->columns(2)
                ->schema([
                    Select::make('navigation_layout')
                        ->options([
                            'top' => 'Top (Horizontal)',
                            'sidebar' => 'Sidebar (Vertical)',
                        ])
                        ->label('Navigation Layout')
                        ->required(),
                ]),
        ];

        if ($this->isAdmin()) {
            $schema[] = Section::make('Footer Links')
                ->description('Customize footer links (admin only).')
                ->schema([
                    Repeater::make('footer_links')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(60),
                            TextInput::make('url')->required()->url(),
                        ])
                        ->reorderable()
                        ->addActionLabel('Add Link')
                        ->columns(2),
                ]);
            $schema[] = Section::make('Platform Maintenance')
                ->description('Admin-only operational controls.')
                ->schema([
                    Toggle::make('maintenance_mode')
                        ->label('Enable Maintenance Mode')
                        ->inline(false)
                        ->helperText('If enabled, non-admin users are shown a maintenance screen.')
                        ->reactive(),
                    KeyValue::make('feature_flags')
                        ->label('Feature Flags')
                        ->addButtonLabel('Add Flag')
                        ->keyLabel('Flag key')
                        ->valueLabel('Enabled (true/false)')
                        ->afterStateHydrated(fn ($component, $state) => $component->state(array_map(fn ($v) => (bool) $v, $state ?? [])))
                        ->helperText('Boolean flags controlling conditional UI / features.'),
                    Actions::make([
                        FormAction::make('enable_all_flags')
                            ->label('Enable All Flags')
                            ->color('success')
                            ->action(function () {
                                $flags = Settings::get('feature_flags', []);
                                $flags = collect($flags)->map(fn () => true)->toArray();
                                Settings::set('feature_flags', $flags);
                                Notification::make()->title('All feature flags enabled')->success()->send();
                                $this->redirect(static::getUrl(), navigate: true);
                            }),
                        FormAction::make('disable_all_flags')
                            ->label('Disable All Flags')
                            ->color('danger')
                            ->action(function () {
                                $flags = Settings::get('feature_flags', []);
                                $flags = collect($flags)->map(fn () => false)->toArray();
                                Settings::set('feature_flags', $flags);
                                Notification::make()->title('All feature flags disabled')->success()->send();
                                $this->redirect(static::getUrl(), navigate: true);
                            }),
                    ])->columnSpanFull(),
                    Grid::make(3)->schema([
                        TextInput::make('branding.marketing_site_url')->label('Marketing URL')->url()->columnSpan(1),
                        TextInput::make('branding.support_email')->label('Support Email')->email()->columnSpan(1),
                        TextInput::make('branding.support_portal_url')->label('Support Portal')->url()->columnSpan(1),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('rate_limits.api_per_minute')
                            ->numeric()
                            ->label('API req / min')
                            ->minValue(10)
                            ->maxValue(10000)
                            ->rules(['integer', 'min:10', 'max:10000']),
                        TextInput::make('rate_limits.ingest_per_hour')
                            ->numeric()
                            ->label('Ingest events / hr')
                            ->minValue(50)
                            ->maxValue(500000)
                            ->rules(['integer', 'min:50', 'max:500000']),
                    ])->columns(2),
                    CheckboxList::make('analytics')
                        ->label('Analytics / Telemetry')
                        ->options([
                            'enable_internal_metrics' => 'Internal Metrics',
                            'enable_error_queue' => 'Error Queue Capture',
                            'mask_user_pii' => 'Mask User PII',
                        ])
                        ->helperText('Toggle operational instrumentation and privacy safeguards.'),
                ]);
        }

        return $schema;
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $isAdmin = $this->isAdmin();

        // Always allow layout preference
        // Persist user-specific layout preference
        UserPreferences::set('navigation_layout', $data['navigation_layout']);
        // Keep a global default only if none previously set (do not overwrite global each user change)
        if (! Settings::get('navigation_layout')) {
            Settings::set('navigation_layout', $data['navigation_layout']);
        }

        if ($isAdmin) {
            $previousMaintenance = (bool) Settings::get('maintenance_mode', false);
            $newMaintenance = (bool) ($data['maintenance_mode'] ?? false);

            Settings::set('footer_links', $data['footer_links'] ?? []);
            Settings::set('maintenance_mode', $newMaintenance);
            Settings::set('feature_flags', $data['feature_flags'] ?? []);
            Settings::set('branding', $data['branding'] ?? []);
            Settings::set('rate_limits', $data['rate_limits'] ?? []);
            Settings::set('analytics', $data['analytics'] ?? []);

            if ($previousMaintenance !== $newMaintenance) {
                event(new \App\Events\MaintenanceModeToggled($newMaintenance, Auth::user()?->email ?? 'system'));
            }
        }
        Notification::make()->title('Settings saved')->success()->send();
        // Redirect to self to rebuild navigation (avoids missing dispatchBrowserEvent in Livewire v3)
        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true; // allow page to show in cluster navigation if cluster nav ever enabled
    }

    protected function authorized(): bool
    {
        // Page is visible to any authenticated user
        return Auth::check();
    }

    protected function isTenantAdmin(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        // Prefer Filament tenant if available, else use accessor
        $team = null;
        try {
            $team = Filament::getTenant();
        } catch (\Throwable $e) { /* ignore */
        }
        if (! $team) {
            $team = method_exists($user, 'currentTeam') ? $user->currentTeam : null;
        }
        if (! $team) {
            return false;
        }

        return isset($team->type) && strcasecmp($team->type, 'Admin') === 0;
    }

    /**
     * Backwards-compatible alias used throughout the page lifecycle.
     * Livewire invoked isAdmin() before we renamed the underlying logic to isTenantAdmin().
     */
    protected function isAdmin(): bool
    {
        return $this->isTenantAdmin();
    }
}
