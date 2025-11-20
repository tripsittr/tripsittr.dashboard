<?php

namespace App\Filament\Venues\Resources;

use App\Filament\Venues\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Venues\Resources\BookingResource\Pages\ViewBooking;
use App\Filament\Venues\Resources\BookingResource\Pages\EditBooking;

use App\Models\Booking;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Bookings';
    protected static ?string $label = 'Bookings';
    protected static ?string $navigationLabel = 'Bookings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('artist_id')
                ->relationship('artist', 'name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('venue_id')
                ->relationship('venue', 'name')
                ->searchable()
                ->required(),
            Forms\Components\DateTimePicker::make('confirmed_at')->required(),
            Forms\Components\KeyValue::make('setlist')->label('Setlist (Song IDs)'),
            Forms\Components\Select::make('payment_status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('artist.name')->label('Artist'),
            Tables\Columns\TextColumn::make('venue.name')->label('Venue'),
            Tables\Columns\TextColumn::make('confirmed_at')->dateTime(),
            Tables\Columns\TextColumn::make('payment_status')->badge(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Venues\Resources\BookingResource\RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'view' => ViewBooking::route('/{record}'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
