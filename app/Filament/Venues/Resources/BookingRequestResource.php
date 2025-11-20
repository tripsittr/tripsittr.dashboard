<?php

namespace App\Filament\Venues\Resources;

use App\Filament\Venues\Resources\BookingRequestResource\Pages\ListBookingRequests;
use App\Filament\Venues\Resources\BookingRequestResource\Pages\ViewBookingRequest;
use App\Filament\Venues\Resources\BookingRequestResource\Pages\EditBookingRequest;

use App\Models\BookingRequest;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;

class BookingRequestResource extends Resource
{
    protected static ?string $model = BookingRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Bookings';
    protected static ?string $label = 'Booking Requests';
    protected static ?string $navigationLabel = 'Booking Requests';

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
            Forms\Components\DateTimePicker::make('start_time')->required(),
            Forms\Components\DateTimePicker::make('end_time')->required(),
            Forms\Components\Textarea::make('notes'),
            Forms\Components\KeyValue::make('setlist')->label('Setlist (Song IDs)'),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'denied' => 'Denied',
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('artist.name')->label('Artist'),
            Tables\Columns\TextColumn::make('venue.name')->label('Venue'),
            Tables\Columns\TextColumn::make('start_time')->dateTime(),
            Tables\Columns\TextColumn::make('end_time')->dateTime(),
            Tables\Columns\TextColumn::make('status')->badge(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingRequests::route('/'),
            'view' => ViewBookingRequest::route('/{record}'),
            'edit' => EditBookingRequest::route('/{record}/edit'),
        ];
    }
}
