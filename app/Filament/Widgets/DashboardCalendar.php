<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class DashboardCalendar extends FullCalendarWidget {
    public function fetchEvents(array $fetchInfo): array {
        return Event::whereBetween('starts_at', [$fetchInfo['start'], $fetchInfo['end']])
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

            Grid::make()
                ->schema([
                    DateTimePicker::make('starts_at')->required(),
                    DateTimePicker::make('ends_at'),
                ]),
        ];
    }
}
