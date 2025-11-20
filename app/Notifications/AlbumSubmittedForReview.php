<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AlbumSubmittedForReview extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Add logic as needed
    }
}
