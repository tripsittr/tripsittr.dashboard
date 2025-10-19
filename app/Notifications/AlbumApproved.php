<?php

namespace App\Notifications;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlbumApproved extends Notification implements ShouldQueue
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
            ->subject('Album Approved: '.$this->album->title)
            ->line('Your album has been approved by the admin team.')
            ->line('Release Date: '.optional($this->album->release_date)->toDateString())
            ->action('View Album', url('/admin/albums/'.$this->album->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'album_id' => $this->album->id,
            'title' => $this->album->title,
            'message' => 'Album approved.',
            'level' => 'success'
        ];
    }
}
