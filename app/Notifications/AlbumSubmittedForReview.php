<?php

namespace App\Notifications;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlbumSubmittedForReview extends Notification implements ShouldQueue
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
            ->subject('Album Submitted for Review: '.$this->album->title)
            ->line('An album has been submitted for review.')
            ->line('Title: '.$this->album->title)
            ->line('Team ID: '.$this->album->team_id)
            ->action('Review Album', url('/admin/approvals'));
    }

    public function toArray($notifiable): array
    {
        return [
            'album_id' => $this->album->id,
            'title' => $this->album->title,
            'message' => 'Album submitted for review.',
            'level' => 'info'
        ];
    }
}
