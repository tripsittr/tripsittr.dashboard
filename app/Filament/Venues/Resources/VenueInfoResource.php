<?php

namespace App\Filament\Venues\Resources;

use App\Filament\Venues\Resources\VenueInfoResource\Pages;
use App\Filament\Venues\Resources\VenueInfoResource\RelationManagers;
use App\Models\VenueInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueInfoResource extends Resource
{
    protected static ?string $model = VenueInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListVenueInfos::route('/'),
            'create' => Pages\CreateVenueInfo::route('/create'),
            'edit' => Pages\EditVenueInfo::route('/{record}/edit'),
        ];
    }
}
