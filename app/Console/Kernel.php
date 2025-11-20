<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('inventory:check-low-stock')->hourly();
        $schedule->command('music:process-album-releases')->everyFifteenMinutes();
        // Refresh Facebook/Instagram tokens daily (refresh tokens expiring within 7 days)
        $schedule->command('social:facebook:refresh --days=7')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
