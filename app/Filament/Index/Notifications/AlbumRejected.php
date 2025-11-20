<?php
namespace App\Filament\Index\Notifications;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlbumRejected extends Notification implements ShouldQueue
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
            ->subject('Album Rejected: '.$this->album->title)
            ->line('Your album was rejected during review.')
            ->when($this->album->rejection_reason, fn($msg)=> $msg->line('Reason: '.$this->album->rejection_reason))
            ->action('Review & Update', url('/admin/albums/'.$this->album->id.'/edit'));
    }

    public function toArray($notifiable): array
    {
        return [
            'album_id' => $this->album->id,
            'title' => $this->album->title,
            'message' => 'Album rejected'.($this->album->rejection_reason?': '.$this->album->rejection_reason:''),
            'level' => 'danger'
        ];
    }
}
