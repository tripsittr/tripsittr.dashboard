<?php

namespace App\Filament\Artists\Clusters\Settings\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms; 
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Account extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.settings.account';
    protected static ?string $cluster = \App\Filament\Artists\Clusters\Settings\Settings::class;
    protected static ?string $navigationIcon = 'heroicon-s-user-circle';
    protected static ?string $navigationLabel = 'Account';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->revealable()
                ->dehydrated(fn($state) => filled($state))
                ->minLength(8)
                ->label('New Password'),
        ])->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        Notification::make()->title('Account updated')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Changes')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-m-check'),
        ];
    }
}