<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Album;
use App\Notifications\AlbumReleased;

class ProcessAlbumReleases extends Command
{
    protected $signature = 'music:process-album-releases';
    protected $description = 'Transition approved albums whose release date has arrived to released status';

    public function handle(): int
    {
        $now = now();
        $albums = Album::query()
            ->whereIn('status', ['approved'])
            ->whereNotNull('release_date')
            ->whereDate('release_date', '<=', $now)
            ->get();

        foreach($albums as $album){
            $album->status = 'released';
            $album->released_at = now();
            $album->save();
            if($album->user_id && $album->user){ optional($album->user)->notify(new AlbumReleased($album)); }
        }
        $this->info('Processed '.$albums->count().' album releases.');
        return Command::SUCCESS;
    }
}
