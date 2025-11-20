<?php

namespace App\Filament\Venues\Resources;

use App\Filament\Venues\Resources\VenueEventResource\Pages;
use App\Models\VenueEvent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueEventResource extends Resource
{
    protected static ?string $model = VenueEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Table columns correspond to the fields defined in the migration file
    // $table->bigIncrements('id');
    // $table->unsignedBigInteger('venue_id')->index('events_venue_id_foreign');
    // $table->string('name');
    // $table->text('description')->nullable();
    // $table->timestamp('starts_at');
    // $table->timestamp('ends_at')->nullable();
    // $table->unsignedBigInteger('team_id')->index('events_team_id_foreign');
    // $table->timestamps();

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('venue_id')
                    ->required()
                    ->numeric(),
                TextInput::make('description')
                    ->maxLength(65535),
                TextInput::make('starts_at')
                    ->required()
                    ->dateTime(),
                TextInput::make('ends_at')
                    ->dateTime(),
                TextInput::make('team_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('venue_id')->label('Venue ID')->sortable(),
                Tables\Columns\TextColumn::make('starts_at')->label('Starts At')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->label('Ends At')->sortable(),
                Tables\Columns\TextColumn::make('team_id')->label('Team ID')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListVenueEvents::route('/'),
            'create' => Pages\CreateVenueEvent::route('/create'),
            'view' => Pages\ViewVenueEvent::route('/{record}'),
            'edit' => Pages\EditVenueEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
