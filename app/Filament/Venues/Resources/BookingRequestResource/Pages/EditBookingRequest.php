<?php

namespace App\Filament\Venues\Resources\BookingRequestResource\Pages;

use App\Filament\Venues\Resources\BookingRequestResource;
use App\Models\Booking;
use App\Models\Event;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditBookingRequest extends EditRecord
{
    protected static string $resource = BookingRequestResource::class;

    /**
     * After save hook to handle approval logic.
     */
    protected function afterSave(): void
    {
        parent::afterSave();

        $record = $this->record;

        // Only act if status is approved and no Booking exists for this request
        if ($record->status === 'approved' && ! Booking::where('request_id', $record->id)->exists()) {
            DB::transaction(function () use ($record) {
                // Create Booking
                $booking = Booking::create([
                    'request_id' => $record->id,
                    'artist_id' => $record->artist_id,
                    'venue_id' => $record->venue_id,
                    'confirmed_at' => now(),
                    'setlist' => $record->setlist,
                    'payment_status' => 'pending',
                ]);

                // Optionally, create/sync Event (if desired)
                // We'll create an Event for the artist's team if not already present for this booking
                $artist = $record->artist;
                $team = $artist->currentTeam();
                if ($team) {
                    $event = Event::create([
                        'name' => 'Performance at '.($record->venue->name ?? 'Venue'),
                        'description' => $record->notes,
                        'starts_at' => $record->start_time,
                        'ends_at' => $record->end_time,
                        'team_id' => $team->id,
                        'venue' => $record->venue->name ?? null,
                        'author_id' => $artist->id,
                        'status' => 'confirmed',
                        'notes' => $record->notes,
                    ]);
                }
            });

            $venueName = $record->venue->name ?? 'Venue';
            $artistName = $record->artist->name ?? 'Artist';
            $date = $record->start_time ? $record->start_time->format('M d, Y H:i') : '';
            Notification::make()
                ->title('Booking approved and synced!')
                ->body("$artistName is now booked at $venueName on $date. Event and setlist have been synced.")
                ->success()
                ->send();
        }
    }
}
