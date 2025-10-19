<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceModeToggled implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $enabled;
    public string $by;

    public function __construct(bool $enabled, string $by)
    {
        $this->enabled = $enabled;
        $this->by = $by;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('system.maintenance');
    }

    public function broadcastAs(): string
    {
        return 'maintenance.toggled';
    }
}
