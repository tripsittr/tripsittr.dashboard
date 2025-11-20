<?php

namespace App\Filament\Index\Pages;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SubscriptionManagement extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-s-credit-card';

    protected static ?string $navigationLabel = 'Manage Subscription';

    protected static string $view = 'filament.pages.subscription-management';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $plan;

    public function mount(): void
    {
        $team = Auth::user()->teams()->first();
        $this->plan = $team && $team->subscribed('default')
            ? $team->subscription('default')->stripe_price
            : null;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Forms\Components\Select::make('plan')
                        ->label('Select a Subscription Plan')
                        ->options([
                            'basic' => 'Basic - $9.99/mo',
                            'premium' => 'Premium - $19.99/mo',
                            'enterprise' => 'Enterprise - $49.99/mo',
                        ])
                        ->required(),
                    Forms\Components\Actions\Action::make('subscribe')
                        ->label('Update Subscription')
                        ->action('updateSubscription')
                        ->requiresConfirmation()
                        ->button()
                        ->successNotificationTitle('Subscription updated successfully'),
                ]),
        ];
    }

    public function updateSubscription(array $data): void
    {
        $team = Auth::user()->team;

        $priceId = config("cashier.plans.{$data['plan']}.price_id");

        if ($team->subscribed('default')) {
            $team->subscription('default')->swap($priceId);
        } else {
            $team->newSubscription('default', $priceId)->create();
        }

        Notification::make()
            ->title('Subscription Updated')
            ->success()
            ->send();
    }
}
