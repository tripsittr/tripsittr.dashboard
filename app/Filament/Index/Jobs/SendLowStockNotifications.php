<?php
namespace App\Filament\Index\Jobs;

use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLowStockNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $teamId = null)
    {
    }

    public function handle(): void
    {
        $query = InventoryItem::query()->whereColumn('stock', '<', 'low_stock_threshold');
        if ($this->teamId) {
            $query->where('team_id', $this->teamId);
        }
        $items = $query->get();
        if ($items->isEmpty()) {
            return;
        }
        $grouped = $items->groupBy('team_id');
        foreach ($grouped as $teamId => $teamItems) {
            $users = User::query()->where('team_id', $teamId)->get();
            foreach ($users as $user) {
                foreach ($teamItems as $item) {
                    $user->notify(new LowStockNotification($item));
                }
            }
        }
    }
}
