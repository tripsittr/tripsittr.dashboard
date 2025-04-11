<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TeamResource extends Resource {
    protected static ?string $model = Team::class;

    protected static ?string $tenantOwnershipRelationshipName = 'users';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool {
        $user = Auth::user();
        return $user && $user->hasRole(['admin', 'super admin']);
    }

    public static function form(Forms\Form $form): Forms\Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('type')->required()
                    ->options([
                        'Solo Artist' => 'Solo Artist',
                        'Band' => 'Band',
                        'Management Agency' => 'Management Agency',
                        'Record Label' => 'Record Label',
                    ]),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('manage_members')
                    ->label('Manage Members')
                    ->url(fn($record) => TeamResource::getUrl('edit', ['record' => $record->id]))
                    ->icon('heroicon-s-users')
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
