<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixSongFilePaths extends Command
{
    protected $signature = 'songs:fix-paths {--dry-run : Only show proposed changes} {--limit=0 : Limit number of records processed}';

    protected $description = 'Attempt to repair legacy song_file paths (e.g., demo/XYZ.mp3) by locating existing files by basename.';

    public function handle(): int
    {
        $dry = (bool)$this->option('dry-run');
        $limit = (int)$this->option('limit');

        $query = Song::query()->where('song_file', 'like', 'demo/%');
        if ($limit > 0) { $query->limit($limit); }
        $total = $query->count();
        $this->info("Scanning {$total} legacy paths...");

        $fixed = 0; $unresolved = 0;

        // Build an index of current public disk audio files by basename
        $this->info('Indexing storage basenames...');
        $index = [];
        $roots = ['audio', 'songs'];
        foreach ($roots as $root) {
            if (!Storage::exists($root)) { continue; }
            foreach (Storage::allFiles($root) as $file) {
                $base = basename($file);
                $index[$base][] = $file; // accumulate
            }
        }
        $this->info('Indexed '.count($index).' unique basenames.');

        $query->chunk(100, function($songs) use (&$fixed, &$unresolved, $index, $dry) {
            foreach ($songs as $song) {
                $legacy = $song->song_file;
                $base = basename($legacy);
                if (!isset($index[$base])) {
                    $this->warn("Unresolved: song ID {$song->id} basename {$base}");
                    $unresolved++;
                    continue;
                }
                $candidates = $index[$base];
                if (count($candidates) > 1) {
                    $this->warn("Ambiguous ({$song->id}): {$base} => ".implode(', ', $candidates));
                    $unresolved++;
                    continue;
                }
                $newPath = $candidates[0];
                if ($dry) {
                    $this->line("Would fix ID {$song->id}: {$legacy} -> {$newPath}");
                } else {
                    $song->song_file = $newPath;
                    $song->save();
                    $this->info("Fixed ID {$song->id}: {$legacy} -> {$newPath}");
                }
                $fixed++;
            }
        });

        $this->line("Done. Fixed: {$fixed}; Unresolved: {$unresolved};");
        if ($dry) { $this->line('Dry-run mode: no changes persisted.'); }

        return Command::SUCCESS;
    }
}
