<?php

namespace App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource\Pages;

use App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVenue extends ViewRecord
{
    use HasRecentHistoryRecorder;

    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => $this->getResource()::canEdit($this->record)),
            Actions\Action::make('Book Venue')
                ->form([
                    \Filament\Forms\Components\DateTimePicker::make('start_time')->required(),
                    \Filament\Forms\Components\DateTimePicker::make('end_time')->required(),
                    \Filament\Forms\Components\Repeater::make('setlist')
                        ->label('Setlist')
                        ->schema([
                            \Filament\Forms\Components\Select::make('song_id')
                                ->label('Song')
                                ->options(function () {
                                    $user = \Illuminate\Support\Facades\Auth::user();
                                    $team = $user?->currentTeam();
                                    if (! $team) return [];
                                    return \App\Models\Song::where('team_id', $team->id)
                                        ->with('album')
                                        ->get()
                                        ->mapWithKeys(function ($song) {
                                            $album = $song->album ? $song->album->title : 'No Album';
                                            $duration = $song->duration ? gmdate('i:s', (int) $song->duration) : '--:--';
                                            $label = $album . ' — ' . $song->title . ' (' . $duration . ')';
                                            return [$song->id => $label];
                                        });
                                })
                                ->searchable()
                                ->required(),
                        ])
                        ->minItems(1)
                        ->maxItems(50),
                    \Filament\Forms\Components\TextInput::make('contact_name')->label('Contact Name'),
                    \Filament\Forms\Components\TextInput::make('contact_email')->label('Contact Email'),
                    \Filament\Forms\Components\TextInput::make('contact_phone')->label('Contact Phone'),
                    \Filament\Forms\Components\Textarea::make('notes')->label('Notes (optional)'),
                ])
                ->action(function (array $data) {
                    $user = Auth::user();

                    // Check for overlapping bookings for this venue
                    $overlap = \App\Models\BookingRequest::where('venue_id', $this->record->id)
                        ->where(function($q) use ($data) {
                            $q->where(function($q2) use ($data) {
                                $q2->where('start_time', '<', $data['end_time'])
                                    ->where('end_time', '>', $data['start_time']);
                            });
                        })
                        ->whereIn('status', ['pending', 'approved'])
                        ->exists();
                    if ($overlap) {
                        \Filament\Notifications\Notification::make()
                            ->title('This venue is already booked for the selected time range!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $bookingRequest = \App\Models\BookingRequest::create([
                        'artist_id' => $user->id,
                        'venue_id' => $this->record->id,
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'setlist' => $data['setlist'] ?? null,
                        'notes' => $data['notes'] ?? null,
                        'status' => 'pending',
                    ]);

                    // Create a DirectMessageThread for this booking request
                    $thread = \App\Models\DirectMessageThread::create([
                        'team_id' => $user->team_id,
                        'subject' => 'Booking Request: '.$this->record->name,
                        'booking_request_id' => $bookingRequest->id,
                    ]);

                    // Add participants: artist and venue owner (if available)
                    \App\Models\DirectMessageParticipant::create([
                        'thread_id' => $thread->id,
                        'user_id' => $user->id,
                    ]);
                    if ($this->record->user_id ?? null) {
                        \App\Models\DirectMessageParticipant::create([
                            'thread_id' => $thread->id,
                            'user_id' => $this->record->user_id,
                        ]);
                    }

                    // Send booking details in the first message
                    $details = "Booking Request Details:\n" .
                        "Artist: {$user->name}\n" .
                        "Start: {$data['start_time']}\n" .
                        "End: {$data['end_time']}\n" .
                        (!empty($data['setlist']) ? "Setlist: ".json_encode($data['setlist'])."\n" : '') .
                        (!empty($data['contact_name']) ? "Contact: {$data['contact_name']} ({$data['contact_email']}, {$data['contact_phone']})\n" : '') .
                        (!empty($data['notes']) ? "Notes: {$data['notes']}\n" : '');
                    \App\Models\DirectMessage::create([
                        'thread_id' => $thread->id,
                        'sender_id' => $user->id,
                        'sender_type' => get_class($user),
                        'body' => $details,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Booking request sent!')
                        ->success()
                        ->send();
                })
                ->modalHeading('Book this Venue')
                ->modalSubmitActionLabel('Send Booking Request'),
        ];
    }
}
