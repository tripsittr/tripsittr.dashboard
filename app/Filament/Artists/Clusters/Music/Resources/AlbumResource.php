<?php

namespace App\Filament\Artists\Clusters\Music\Resources;

use App\Filament\Artists\Clusters\Music\Resources\AlbumResource\Pages;
use App\Models\Album;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class; // Ensure the model is set here

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Music\Music::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->disabled(fn ($record) => $record && in_array($record->status, ['in_review', 'approved', 'released'])),
                DatePicker::make('release_date')->disabled(fn ($record) => $record && in_array($record->status, ['in_review', 'approved', 'released']))->helperText('When should this album release (used after approval).'),
                \Filament\Forms\Components\Placeholder::make('status')
                    ->label('Workflow Status')
                    ->content(fn ($record) => $record?->status ?? 'draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('status')->badge()->colors([
                    'secondary' => 'draft',
                    'warning' => 'in_review',
                    'success' => 'approved',
                    'info' => 'scheduled',
                    'success' => 'released',
                    'danger' => 'rejected',
                ])->sortable(),
                TextColumn::make('release_date')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('submit_for_review')
                    ->label('Submit for Review')
                    ->visible(fn ($record) => $record && FacadesAuth::user()->can('submit', $record))
                    ->requiresConfirmation()
                    ->action(function (Album $record) {
                        $record->submitForReview(FacadesAuth::id());
                        // notify admins
                        foreach (\App\Support\Admin::users() as $admin) {
                            $admin->notify(new \App\Notifications\AlbumSubmittedForReview($record));
                        }
                    })
                    ->color('warning'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn ($record) => $record && FacadesAuth::user()->can('update', $record)),
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
            'view' => Pages\ViewAlbum::route('/{record}'), // Add the View page route
        ];
    }
}
