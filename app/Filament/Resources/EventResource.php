<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Widgets\DashboardCalendar;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource {
    protected static ?string $model = Event::class;

    protected static ?string $navigationGroup = 'Planning & Events';

    public static function getWidgets(): array {
        return [
            DashboardCalendar::class,
        ];
    }

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('ends_at'),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('team.name')->label('Team'),
            ])
            ->filters([
                // Add filters if needed
            ]);
    }

    public static function getRelations(): array {
        return [];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
