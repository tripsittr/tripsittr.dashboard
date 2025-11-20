<?php
namespace App\Filament\Admin\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('team_avatar')
                    ->image()
                    ->avatar(),
                TextInput::make('name')->required()
                    ->helperText('Artist, Band, or Company Name. (e.g. "The Beatles", "Sony Music")'),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $plan = session('selected_plan', config('plans.default_plan'));
        // Map plan slug to an internal team type silently
        $planTypeMap = [
            'solo' => 'Solo Artist',
            'band' => 'Band',
        ];
        $type = $planTypeMap[$plan] ?? 'Solo Artist';
        $team = Team::create(array_merge($data, [
            'plan_slug' => $plan,
            'plan_started_at' => now(),
            'type' => $type,
        ]));

        // Use syncWithoutDetaching to avoid accidental duplicate pivot rows if creation logic re-runs.
        $team->users()->syncWithoutDetaching([Auth::id()]);

        // Attempt to create a Stripe subscription stub (only if price configured)
        $planConfig = config("plans.plans.$plan") ?? [];
        $priceId = $planConfig['stripe_price_id'] ?? null;
        $owner = Auth::user();
        if ($priceId && $team && method_exists($team, 'createOrGetStripeCustomer')) {
            try {
                $team->createOrGetStripeCustomer();
                if (! $team->subscription('default')) {
                    $subscription = $team->newSubscription('default', $priceId)
                        ->quantity(1)
                        ->create();
                    // Ensure team_id is set (Cashier uses the Billable model's key but we added team_id column)
                    if ($subscription && empty($subscription->team_id)) {
                        DB::table('subscriptions')->where('id', $subscription->id)->update(['team_id' => $team->id]);
                    }
                    \App\Services\LogActivity::record('subscription.created', 'Team', $team->id, ['plan' => $plan, 'price' => $priceId, 'subscription_id' => $subscription->id ?? null], $team->id);
                }
            } catch (\Throwable $e) {
                logger()->warning('Stripe subscription stub creation failed: '.$e->getMessage());
            }
        }

        return $team;
    }
}
