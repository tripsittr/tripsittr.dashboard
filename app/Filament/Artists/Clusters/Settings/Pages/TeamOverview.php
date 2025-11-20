<?php

namespace App\Filament\Artists\Clusters\Settings\Pages;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TeamOverview extends Page implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationLabel = 'Team';

    protected static ?string $title = 'Team';

    protected static ?string $slug = 'team';

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Settings\Settings::class;

    protected static string $view = 'filament.settings.team.profile';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        $team = $this->getTeam();
        $user = Auth::user();
        $canManage = $user && $team && ($user->hasAnyRole(['Admin', 'Manager']));

        return $table
            ->query(fn () => $team ? $team->users()->with('roles')->orderBy('users.name') : \App\Models\User::query()->whereRaw('1=0'))
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->toggleable()->wrap(),
                TextColumn::make('roles.name')
                    ->label('Primary Role')
                    ->formatStateUsing(fn ($state, $record) => $record->roles->first()?->name ?? '—'),
                TextColumn::make('pivot.created_at')
                    ->since()
                    ->label('Joined'),
            ])
            ->headerActions([
                Action::make('invite')
                    ->label('Invite')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn () => $canManage)
                    ->form([
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'Member' => 'Member',
                                'Manager' => 'Manager',
                                'Admin' => 'Admin',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) use ($team) {
                        if (! $team) {
                            Notification::make()->title('No team context')->danger()->send();

                            return;
                        }
                        // Seat limit check
                        if (! $team->hasSeatAvailable()) {
                            Notification::make()->title('Seat limit reached')->danger()->body('Upgrade plan or remove a member first.')->send();

                            return;
                        }
                        $email = strtolower(trim($data['email']));
                        $existingUser = \App\Models\User::where('email', $email)->first();
                        if ($existingUser && $team->users()->where('users.id', $existingUser->id)->exists()) {
                            Notification::make()->title('User already a member')->warning()->send();

                            return;
                        }
                        $invitation = \App\Models\Invitation::create([
                            'team_id' => $team->id,
                            'email' => $email,
                            'role' => $data['role'] ?? null,
                        ]);
                        Notification::make()->title('Invitation sent')->success()->body($email)->send();
                    }),
            ])
            ->actions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn ($record) => $canManage)
                    ->url(fn ($record) => \App\Filament\Artists\Clusters\Settings\Pages\ManageMember::getUrl(['member' => $record]))
                    ->openUrlInNewTab(),
                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $canManage && Auth::id() !== $record->id)
                    ->action(function ($record) use ($team) {
                        if (! $team) {
                            return;
                        }
                        $team->users()->detach($record->id);
                        Notification::make()->title('Member removed')->success()->send();
                    }),
            ])
            ->defaultSort('name');
    }

    public function getTeam()
    {
        return Auth::user()?->current_team;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $team = $this->getTeam();
        if ($team) {
            $this->form->fill($team->only([
                'name', 'team_avatar', 'formation_date', 'genre', 'website', 'instagram', 'twitter', 'facebook', 'youtube', 'email', 'phone',
            ]));
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->getTeam())
            ->schema([
                TextInput::make('name')->required(),
                FileUpload::make('team_avatar')->image()->avatar(),
                DatePicker::make('formation_date')->label('Formation Date')->nullable(),
                Select::make('genre')->label('Genres')->multiple()->options($this->genreOptions())->searchable()->nullable()->preload(),
                Section::make('Social Media & Contact')
                    ->schema([
                        TextInput::make('website')->label('Website')->url()->placeholder('https://teamwebsite.com')->nullable(),
                        TextInput::make('instagram')->label('Instagram')->prefix('https://instagram.com/')->placeholder('handle')->nullable(),
                        TextInput::make('twitter')->label('Twitter/X')->prefix('https://twitter.com/')->placeholder('handle')->nullable(),
                        TextInput::make('facebook')->label('Facebook')->prefix('https://facebook.com/')->placeholder('page')->nullable(),
                        TextInput::make('youtube')->label('YouTube Channel')->prefix('https://youtube.com/')->placeholder('channel')->nullable(),
                        TextInput::make('email')->label('Contact Email')->email()->placeholder('contact@mail.com')->nullable(),
                        TextInput::make('phone')->label('Contact Phone')->mask('(999) 999-9999')->nullable(),
                    ])->collapsible(),
            ])
            ->statePath('data');
    }

    protected function genreOptions(): array
    {
        return [
            'Drum & Bass' => 'Drum & Bass', 'Dubstep' => 'Dubstep', 'Grime' => 'Grime', 'Jersey Club' => 'Jersey Club', 'Jungle' => 'Jungle', 'Acid House' => 'Acid House', 'Afro House' => 'Afro House', 'Afrobeats' => 'Afrobeats', 'Amapiano' => 'Amapiano', 'Deep House' => 'Deep House', 'Disco' => 'Disco', 'Garage' => 'Garage', 'Hardstyle' => 'Hardstyle', 'House' => 'House', 'Minimal' => 'Minimal', 'Progressive House' => 'Progressive House', 'Psytrance' => 'Psytrance', 'Slap House' => 'Slap House', 'Tech House' => 'Tech House', 'Techno' => 'Techno', 'Trance' => 'Trance', 'Ambient' => 'Ambient', 'Chill-Out' => 'Chill-Out', 'Downtempo' => 'Downtempo', 'Electro' => 'Electro', 'IDM' => 'IDM', 'Trip Hop' => 'Trip Hop', 'Boom Bap' => 'Boom Bap', 'Drill' => 'Drill', 'Lo-Fi' => 'Lo-Fi', 'Phonk' => 'Phonk', 'Reggaeton' => 'Reggaeton', 'R&B' => 'R&B', 'Trap' => 'Trap', 'West Coast' => 'West Coast', 'African' => 'African', 'Asian' => 'Asian', 'Bossa Nova' => 'Bossa Nova', 'Brazilian' => 'Brazilian', 'Caribbean' => 'Caribbean', 'Cuban' => 'Cuban', 'Dancehall' => 'Dancehall', 'Indian' => 'Indian', 'Latin American' => 'Latin American', 'Middle Eastern' => 'Middle Eastern', 'Reggae' => 'Reggae', 'Blues' => 'Blues', 'Classic R&B' => 'Classic R&B', 'Classical' => 'Classical', 'Country' => 'Country', 'Folk' => 'Folk', 'Funk' => 'Funk', 'Gospel' => 'Gospel', 'Indie Rock' => 'Indie Rock', 'Jazz' => 'Jazz', 'Metal' => 'Metal', 'Post-Punk' => 'Post-Punk', 'Punk' => 'Punk', 'Rock' => 'Rock', 'Soul' => 'Soul', 'EDM' => 'EDM', 'Electropop' => 'Electropop', 'Future House' => 'Future House', 'Hyperpop' => 'Hyperpop', 'K-pop' => 'K-pop', 'Moombahton' => 'Moombahton', 'Pop' => 'Pop', 'Synthwave' => 'Synthwave', 'Tropical House' => 'Tropical House', 'Cinematic' => 'Cinematic', 'Video Game' => 'Video Game',
        ];
    }

    public function save(): void
    {
        $team = $this->getTeam();
        if (! $team) {
            return;
        }
        $team->update($this->form->getState());
        \Filament\Notifications\Notification::make()->title('Team updated')->success()->send();
    }
}
