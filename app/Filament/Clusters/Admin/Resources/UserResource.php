<?php

namespace App\Filament\Clusters\Admin\Resources;

use App\Filament\Clusters\Admin;
use App\Filament\Clusters\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource {
    protected static ?string $model = User::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $cluster = Admin::class;
    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form {
        $user = Auth::user();
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('type')
                    ->visible(Filament::getTenant()->type == 'Admin')
                    ->disabled(Filament::getTenant()->type !== 'Admin'),
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
