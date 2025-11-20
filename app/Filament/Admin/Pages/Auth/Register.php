<?php
namespace App\Filament\Admin\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    // Use a custom Blade view instead of the default Filament view
    protected static string $view = 'filament.pages.auth.register';
    public ?string $selectedPlan = null;

    public function mount(): void
    {
        parent::mount();
        $slug = strtolower(request()->query('plan',''));
        $plans = config('plans.plans');
        $this->selectedPlan = array_key_exists($slug, $plans) ? $slug : config('plans.default_plan');
        session(['selected_plan' => $this->selectedPlan]);
    }

    // Define the Filament form schema correctly so it is used by the base page
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    TextInput::make('first_name')
                        ->label('First Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->label('Last Name')
                        ->required()
                        ->maxLength(255),
                ]),
                TextInput::make('phone')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'We’ll use your phone number to send important account updates via text message.')
                    ->label(fn () => new HtmlString('Phone <span class="text-gray-400">(Optional)</span>'))
                    ->placeholder('Phone')
                    ->tel(),
                Fieldset::make('Account')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('plan')
                            ->label('Selected Plan')
                            ->content(fn() => ucfirst($this->selectedPlan ?? '')),
                        $this->getEmailFormComponent()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->columnSpanFull(),
                        $this->getPasswordFormComponent()
                            ->required()
                            ->columnSpanFull(),
                        $this->getPasswordConfirmationFormComponent()
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ])
            // Keep the same state path the base page expects
            ->statePath('data');
    }

    protected function handleRegistration(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $fullName = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));

            return User::create([
                'name' => $fullName ?: ($data['email'] ?? 'User'),
                'email' => $data['email'],
                // Password is already hashed by Filament's Password component dehydration
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
            ]);
        });
    }
}
