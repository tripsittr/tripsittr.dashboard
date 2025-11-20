<?php
namespace App\Filament\Index\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions;
use App\Models\Event;
use App\Models\Venue;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class DashboardCalendar extends FullCalendarWidget {
    public function fetchEvents(array $fetchInfo): array {

        return Event::whereBetween('starts_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->where('team_id', Filament::getTenant()->id)
            ->get()
            ->map(fn(Event $event) => [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->starts_at->toIso8601String(),
                'end' => $event->ends_at?->toIso8601String(),
            ])
            ->toArray();
    }

    public Model | string | null $model = Event::class;

    protected function headerActions(): array {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['team_id'] = Filament::getTenant()->id;

                    return $data;
                }),
        ];
    }

    protected function modalActions(): array {
        return [
            Actions\EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['team_id'] = Filament::getTenant()->id;

                    return $data;
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function viewAction(): Action {
        return Actions\ViewAction::make();
    }

    public function getFormSchema(): array {
        return [
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
            ]),
            RichEditor::make('notes')->columnSpanFull(),
        ];
    }
}
