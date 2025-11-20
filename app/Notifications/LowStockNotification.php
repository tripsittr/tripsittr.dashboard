<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
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
