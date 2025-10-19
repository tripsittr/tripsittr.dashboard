<?php

namespace App\Filament\Clusters\Admin\Resources\TeamResource\Pages;

use App\Filament\Clusters\Admin\Resources\TeamResource;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageTeamUsers extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = TeamResource::class;

    protected static string $view = 'filament.admin.team.manage-users';

    protected static bool $shouldRegisterNavigation = false;

    public Team $record;

    public function mount(Team $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        $team = $this->record;

        return $table
            ->query(fn () => $team->users()->with('roles')->orderBy('users.name'))
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles.name')->label('Roles')->formatStateUsing(fn ($state, $record) => $record->roles->pluck('name')->join(', ')),
            ])
            ->headerActions([
                Action::make('add_user')
                    ->label('Add User')
                    ->form([
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\Select::make('role')->options([
                            'Member' => 'Member',
                            'Manager' => 'Manager',
                            'Admin' => 'Admin',
                        ])->required(),
                    ])
                    ->action(function (array $data) use ($team) {
                        $email = strtolower(trim($data['email']));
                        $user = User::where('email', $email)->first();
                        if ($user) {
                            if ($team->users()->where('users.id', $user->id)->exists()) {
                                Notification::make()->title('User already a member')->warning()->send();

                                return;
                            }
                            $team->users()->attach($user->id);
                            $user->assignRole($data['role']);
                            Notification::make()->title('User added')->success()->send();
                        } else {
                            Invitation::create([
                                'team_id' => $team->id,
                                'email' => $email,
                                'role' => $data['role'],
                            ]);
                            Notification::make()->title('Invitation sent')->success()->send();
                        }
                    }),
            ])
            ->actions([
                Action::make('edit_roles')
                    ->label('Edit Roles')
                    ->form([
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->options([
                                'Member' => 'Member',
                                'Manager' => 'Manager',
                                'Admin' => 'Admin',
                            ])
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->syncRoles($data['roles']);
                        Notification::make()->title('Roles updated')->success()->send();
                    }),
                Action::make('remove')
                    ->label('Remove')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (User $record) use ($team) {
                        $team->users()->detach($record->id);
                        Notification::make()->title('User removed')->success()->send();
                    }),
            ]);
    }

    public function getInvitations()
    {
        return Invitation::where('team_id', $this->record->id)->latest()->get();
    }
}
