<?php
namespace App\Filament\Index\Notifications;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Notification;

class AlbumActivityNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(public Album $album, public string $verb)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'album_id' => $this->album->id,
            'title' => $this->album->title,
            'verb' => $this->verb,
            'message' => "Album {$this->verb}: {$this->album->title}",
            'level' => match($this->verb){
                'created' => 'info',
                'updated' => 'info',
                'deleted' => 'danger',
                default => 'info'
            },
        ]);
    }

    public function toBroadcast($notifiable): DatabaseMessage
    {
        return $this->toDatabase($notifiable);
    }

    public function broadcastType(): string
    {
        return 'album.activity';
    }

    public function broadcastOn(): array
    {
        // Broadcast on a tenant-scoped channel if possible, fallback to public
        $teamId = $this->album->team_id ?? null;
        return $teamId ? [new PrivateChannel('team.'.$teamId.'.albums')] : [];
    }
}
