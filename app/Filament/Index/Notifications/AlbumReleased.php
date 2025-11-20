<?php
namespace App\Filament\Index\Notifications;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlbumReleased extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Album $album) {}

    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Album Released: '.$this->album->title)
            ->line('Your album is now released!')
            ->action('View Album', url('/admin/albums/'.$this->album->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'album_id' => $this->album->id,
            'title' => $this->album->title,
            'message' => 'Album released.',
            'level' => 'success'
        ];
    }
}
