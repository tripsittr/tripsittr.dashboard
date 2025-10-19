<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\InventoryItem;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InventoryItem $item)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'message' => "Low stock alert: {$this->item->name} (SKU: {$this->item->sku}) has only {$this->item->stock} units remaining.",
            'inventory_item_id' => $this->item->id,
            'level' => 'warning',
        ]);
    }
}
