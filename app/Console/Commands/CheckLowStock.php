<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendLowStockNotifications;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock {teamId?}';
    protected $description = 'Dispatch low stock notifications (optionally for a single team)';

    public function handle(): int
    {
        $teamId = $this->argument('teamId');
        SendLowStockNotifications::dispatch($teamId ? (int) $teamId : null);
        $this->info('Low stock notification job dispatched'.($teamId ? " for team {$teamId}" : ''));
        return self::SUCCESS;
    }
}
