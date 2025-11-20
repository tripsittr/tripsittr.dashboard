<?php

namespace App\Filament\Artists\Clusters\Settings\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL; // for home URL fallback

class Billing extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.settings.billing';

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Settings\Settings::class;

    protected static ?string $navigationIcon = 'heroicon-s-credit-card';

    protected static ?string $navigationLabel = 'Billing';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $team = $user?->current_team;
        if ($team) {
            $this->form->fill([
                'plan' => $team->plan_slug,
                'seats' => $team->usedSeats().' / '.$team->maxSeats(),
                'stripe_customer' => $team->stripe_id ?: 'Not created',
            ]);
        }
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Fieldset::make('Subscription')
                ->schema([
                    Placeholder::make('plan')->label('Current Plan'),
                    Placeholder::make('seats')->label('Seat Usage'),
                    Placeholder::make('stripe_customer')->label('Stripe Customer ID'),
                ]),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('managePortal')
                ->label('Open Customer Portal')
                ->icon('heroicon-s-arrow-top-right-on-square')
                ->action(function () {
                    $user = Auth::user();
                    $team = $user?->current_team;
                    if (! $team) {
                        Notification::make()->title('No active team to manage billing')->danger()->send();

                        return;
                    }
                    try {
                        // Ensure Stripe customer exists for the team
                        if (! $team->stripe_id && method_exists($team, 'createOrGetStripeCustomer')) {
                            $team->createOrGetStripeCustomer();
                            // Refresh form state with new customer id
                            $this->form->fill(array_merge($this->form->getState(), [
                                'stripe_customer' => $team->stripe_id,
                            ]));
                        }
                        if (! $team->stripe_id) {
                            Notification::make()
                                ->title('Stripe customer not initialized')
                                ->info()
                                ->send();

                            return;
                        }
                        // Build a safe return URL. Filament's dashboard route appears to require a {tenant} parameter.
                        // If we can't generate it, fall back to Filament's home URL or root.
                        $returnUrl = URL::to('/');
                        try {
                            // Attempt with team id as tenant parameter
                            $returnUrl = route('filament.admin.pages.dashboard', ['tenant' => $team->getKey()]);
                        } catch (\Throwable $routeEx) {
                            logger()->warning('Dashboard route generation (with tenant) failed, falling back', [
                                'team_id' => $team->id,
                                'error' => $routeEx->getMessage(),
                            ]);
                            // Secondary fallback: Filament provided home URL (may still require tenancy context)
                            try {
                                $home = Filament::getHomeUrl();
                                if ($home) {
                                    $returnUrl = $home;
                                }
                            } catch (\Throwable $e2) {
                                // ignore, keep base URL
                            }
                        }

                        $url = $team->billingPortalUrl($returnUrl);
                        logger()->info('Opening Stripe billing portal', [
                            'team_id' => $team->id,
                            'stripe_customer' => $team->stripe_id,
                            'return_url' => $returnUrl,
                        ]);

                        return redirect()->away($url);
                    } catch (\Throwable $e) {
                        logger()->error('Billing portal open failed: '.$e->getMessage(), ['exception' => $e]);
                        Notification::make()
                            ->title('Unable to open portal')
                            ->body('If this persists, verify Stripe API keys & billing portal settings.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
