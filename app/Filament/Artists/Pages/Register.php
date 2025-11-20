<?php

namespace App\Filament\Artists\Pages;

use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';

    public ?string $selectedPlan = null;

    public function mount(): void
    {
        parent::mount();
        $slug = strtolower(request()->query('plan', ''));
        $plans = config('plans.plans');
        $this->selectedPlan = array_key_exists($slug, $plans) ? $slug : config('plans.default_plan');
        session(['selected_plan' => $this->selectedPlan]);
    }

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
                            ->content(fn () => ucfirst($this->selectedPlan ?? '')),
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
            ]);
    }
}
