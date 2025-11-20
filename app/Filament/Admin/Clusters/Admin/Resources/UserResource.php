<?php

namespace App\Filament\Admin\Clusters\Admin\Resources;

use App\Filament\Admin\Clusters\Admin\Admin;
use App\Filament\Admin\Clusters\Admin\Resources\RoleResource as AdminRoleResource;
use App\Filament\Admin\Clusters\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserType;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $cluster = Admin::class;

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),
                Select::make('type')
                    ->label('User Type')
                    ->options(fn () => UserType::query()->orderBy('name')->pluck('name', 'name')->toArray())
                    ->native(false)
                    ->searchable()
                    ->visible(fn () => Auth::user()?->type === 'Admin' || Auth::user()?->hasRole('Admin'))
                    ->disabled(fn () => ! (Auth::user()?->type === 'Admin' || Auth::user()?->hasRole('Admin'))),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->hidden(fn ($record) => $record) // Hide if editing
                    ->required(fn ($record) => ! $record) // Required if creating
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->hidden(fn ($record) => $record) // Hide if editing
                    ->required(fn ($record) => ! $record) // Required if creating
                    ->maxLength(255),

                CheckboxList::make('permission_ids')
                    ->label('Permissions')
                    ->options(function () {
                        $tenant = Filament::getTenant();
                        $query = Permission::query()->orderBy('name')->where('guard_name', 'web');
                        if ($tenant) {
                            $query->where('team_id', $tenant->id);
                        } else {
                            $query->whereNull('team_id');
                        }

                        return $query->pluck('name', 'id')->toArray();
                    })
                    ->columns(3)
                    ->gridDirection('row')
                    ->helperText('Direct permissions for this user (team-scoped).')
                    ->visible(fn ($record) => filled($record))
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // Preload direct permissions for this user under the current tenant
                        if (! $record) {
                            return;
                        }
                        $tenant = Filament::getTenant();
                        if ($tenant) {
                            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
                        }
                        $ids = $record->getDirectPermissions()->pluck('id')->all();
                        $component->state($ids);
                    }),

                CheckboxList::make('role_ids')
                    ->label('Roles')
                    ->options(fn () => AdminRoleResource::getEloquentQuery()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->columns(3)
                    ->gridDirection('row')
                    ->helperText('Assign one or more roles. Permissions can be managed per role in the Roles manager.')
                    ->visible(fn ($record) => filled($record))
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if (! $record) {
                            return;
                        }
                        $tenant = Filament::getTenant();
                        $roleIds = $record->roles()
                            ->when($tenant, fn ($q) => $q->where('roles.team_id', $tenant->id), fn ($q) => $q->whereNull('roles.team_id'))
                            ->pluck('roles.id')
                            ->all();
                        $component->state($roleIds);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('type')->label('User Type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view')
                    ->label('View Profile')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->id]))
                    ->icon('heroicon-s-eye')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),

        ];
    }
}
