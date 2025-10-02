<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\DB;

class Register extends BaseRegister
{
    // Use a custom Blade view instead of the default Filament view
    protected static string $view = 'filament.pages.auth.register';

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
                    ->label('Phone')
                    ->placeholder('Phone')
                    ->tel()
                    ->required(),
                Fieldset::make('Account')
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
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
