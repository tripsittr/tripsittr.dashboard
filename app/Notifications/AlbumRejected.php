<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AlbumRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reason = '')
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your album was rejected')
            ->line('Unfortunately, your album submission was rejected.')
            ->line('Reason: ' . ($this->reason ?: 'No reason provided.'));
    }
}
