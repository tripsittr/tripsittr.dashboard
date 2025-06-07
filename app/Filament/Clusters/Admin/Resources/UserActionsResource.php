<?php

namespace App\Filament\Clusters\Admin\Resources;

use App\Filament\Clusters\Admin;
use App\Filament\Clusters\Admin\Resources\UserActionsResource\Pages;
use App\Models\UserAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserActionsResource extends Resource
{
    protected static ?string $model = UserAction::class;

    protected static ?string $navigationIcon = 'fas-history';

    protected static ?string $cluster = Admin::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('action_type')
                    ->options(new UserAction()->getActionTypes())
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->default(Auth::user()->id)
                    ->options(
                        \App\Models\User::all()->pluck('name', 'id')
                    ),
                Forms\Components\Select::make('team_id')
                    ->required()
                    ->default(Filament::getTenant()->id)
                    ->options(
                        \App\Models\Team::all()->pluck('name', 'id')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUserActions::route('/'),
            'create' => Pages\CreateUserActions::route('/create'),
            'edit' => Pages\EditUserActions::route('/{record}/edit'),
        ];
    }
}
