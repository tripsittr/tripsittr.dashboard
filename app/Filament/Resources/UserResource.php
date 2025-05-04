<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Team;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Traits\HasRoles;

class UserResource extends Resource {
    protected static ?string $model = User::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 15;

    public static function shouldRegisterNavigation(): bool {
        if (Filament::getTenant()->type === 'Admin') {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form {
        $user = Auth::user();
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->hidden(fn($record) => $record) // Hide if editing
                    ->required(fn($record) => !$record) // Required if creating
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->hidden(fn($record) => $record) // Hide if editing
                    ->required(fn($record) => !$record) // Required if creating
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table {
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
                    ->url(fn($record) => UserResource::getUrl('view', ['record' => $record->id]))
                    ->icon('heroicon-s-eye')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),

        ];
    }
}
