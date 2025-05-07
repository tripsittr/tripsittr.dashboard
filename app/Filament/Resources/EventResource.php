<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Widgets\DashboardCalendar;
use App\Models\Event;
use App\Models\Venue;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource {
    protected static ?string $model = Event::class;

    protected static ?string $navigationGroup = 'Events';
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    // protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Grid::make(3)
                    ->schema([
                        DateTimePicker::make('starts_at')->required(),
                        DateTimePicker::make('ends_at'),
                        Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ]),
                    ]),
                Grid::make(3)
                    ->schema([
                        Select::make('venue')
                            ->options(Venue::all()->pluck('name', 'id')),
                        Select::make('type')
                            ->options([
                                'meeting' => 'Meeting',
                                'event' => 'Event',
                                'task' => 'Task',
                                'reminder' => 'Reminder',
                                'holiday' => 'Holiday',
                                'vacation' => 'Vacation',
                                'other' => 'Other',
                            ])
                            ->default('event')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->default('pending'),
                ]),
                Section::make('Contact')
                    ->description('Contact information for the event')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('contact_name')->label('Contact Name'),
                        TextInput::make('contact_email')->label('Contact Email'),
                        TextInput::make('contact_phone')->label('Contact Phone'),
                        TextInput::make('contact_website')->label('Contact Website'),
                        TextInput::make('contact_address')->label('Contact Address'),
                        TextInput::make('contact_address2')->label('Contact Address 2'),
                        TextInput::make('contact_city')->label('Contact City'),
                        TextInput::make('contact_state')->label('Contact State'),
                        TextInput::make('contact_zip')->label('Contact Zip'),
                        TextInput::make('contact_country')->label('Contact Country'),
                    ]),
                RichEditor::make('notes')->columnSpanFull(),
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->visible(Filament::getTenant()->type == 'Admin' || Auth::user()->type == 'Admin')
                    ->required(),
                ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('team.name')->label('Team')->visible(Filament::getTenant() == 'Admin'),
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
