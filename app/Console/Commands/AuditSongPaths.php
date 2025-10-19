<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class AuditSongPaths extends Command
{
    protected $signature = 'songs:audit-paths {--sample=10 : Sample size to display}';
    protected $description = 'Audit song_file values: show counts of existing vs missing files and sample details.';

    public function handle(): int
    {
        $sampleSize = (int)$this->option('sample');
        $total = Song::count();
        $missing = 0; $present = 0;
        $missingExamples = []; $presentExamples = [];

        Song::chunk(200, function($songs) use (&$missing,&$present,&$missingExamples,&$presentExamples,$sampleSize) {
            foreach ($songs as $song) {
                $abs = $song->resolveSongAbsolutePath();
                if ($abs) {
                    $present++;
                    if (count($presentExamples) < $sampleSize) {
                        $presentExamples[] = [$song->id, $song->song_file, $abs];
                    }
                } else {
                    $missing++;
                    if (count($missingExamples) < $sampleSize) {
                        $missingExamples[] = [$song->id, $song->song_file];
                    }
                }
            }
        });

        $this->info("Total songs: {$total}");
        $this->info("Present files: {$present}");
        $this->info("Missing files: {$missing}");

        $this->line("\nSample Present:");
        foreach ($presentExamples as $row) {
            [$id,$stored,$abs] = $row; $this->line("#{$id} stored='{$stored}' -> {$abs}");
        }

        $this->line("\nSample Missing:");
        foreach ($missingExamples as $row) {
            [$id,$stored] = $row; $this->line("#{$id} stored='{$stored}'");
        }

        return Command::SUCCESS;
    }
}
