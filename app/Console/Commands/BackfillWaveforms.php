<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\Audio\AudioMetadataExtractor;
use Illuminate\Console\Command;

class BackfillWaveforms extends Command
{
    protected $signature = 'songs:backfill-waveforms
        {--bars=400 : Number of waveform bars to generate}
        {--force : Regenerate even if waveform_data already exists}
        {--repair-paths : Attempt to repair missing file paths by scanning storage}
        {--only-ids= : Comma-separated list of song IDs to process}
        {--export-missing= : Write list of missing file references to this path (CSV)}';

    protected $description = 'Generate and store waveform_data for songs using ffmpeg';

    public function handle(AudioMetadataExtractor $extractor): int
    {
        $bars = (int) $this->option('bars');
        $force = (bool) $this->option('force');
        $repair = (bool) $this->option('repair-paths');
        $onlyIdsOpt = (string) ($this->option('only-ids') ?? '');
        $exportMissing = $this->option('export-missing');

        $ids = [];
        if ($onlyIdsOpt !== '') {
            $ids = collect(explode(',', $onlyIdsOpt))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v > 0)
                ->values()
                ->all();
        }

        $query = Song::query();
        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $total = $query->count();
        $this->info("Processing {$total} songs...");

        // Build an index of available audio files for path repair (basename => [relative paths]) if requested.
        $fileIndex = [];
        if ($repair) {
            $this->info('Building file index for repair...');
            $root = storage_path('app/public');
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
            $allowedExt = ['mp3','wav','flac','ogg','m4a','webm'];
            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if (! $file->isFile()) { continue; }
                $ext = strtolower($file->getExtension());
                if (! in_array($ext, $allowedExt, true)) { continue; }
                $basename = $file->getFilename();
                $relative = ltrim(str_replace($root, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                $fileIndex[$basename][] = $relative; // may be multiple
            }
            $this->info('Indexed '.count($fileIndex).' audio basenames.');
        }

        $processed = 0; $updated = 0; $skipped = 0; $errors = 0; $repaired = 0; $missing = [];

        $query->orderBy('id')->chunk(100, function ($songs) use (&$processed, &$updated, &$skipped, &$errors, &$repaired, &$missing, $extractor, $bars, $force, $repair, $fileIndex) {
            foreach ($songs as $song) {
                $processed++;

                if (! $force) {
                    $existing = $song->waveform_data;
                    if (is_array($existing) && ! empty($existing)) { $skipped++; continue; }
                    if (is_string($existing)) {
                        $decoded = json_decode($existing, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && ! empty($decoded)) { $skipped++; continue; }
                    }
                }

                // Resolve absolute path
                $path = $song->resolveSongAbsolutePath();
                $relative = $song->song_file;
                if (! $path && $relative) {
                    // Attempt repair if enabled
                    if ($repair) {
                        $base = basename($relative);
                        if (isset($fileIndex[$base])) {
                            $matches = $fileIndex[$base];
                            if (count($matches) === 1) {
                                $newRel = $matches[0];
                                $this->line("Repaired path for song ID {$song->id}: {$relative} -> {$newRel}");
                                $song->song_file = $newRel;
                                $song->save();
                                $path = $song->resolveSongAbsolutePath();
                                $repaired++;
                            } else {
                                $this->warn("Ambiguous repair for {$relative} (".count($matches)." candidates)");
                            }
                        }
                    }
                }

                if (! $path || ! is_file($path)) {
                    if ($relative) { $this->warn("Missing file: {$relative}"); $missing[] = $relative; }
                    $skipped++;
                    continue;
                }

                try {
                    $wave = $extractor->generateWaveformData($path, $bars);
                    if (! empty($wave)) {
                        $song->waveform_data = $wave; // cast to array
                        $song->save();
                        $updated++;
                    } else {
                        $this->warn("No waveform generated for ID {$song->id}");
                        $skipped++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $this->error("Error processing song ID {$song->id}: " . $e->getMessage());
                }
            }
        });

        $this->line("Processed: {$processed}; Updated: {$updated}; Skipped: {$skipped}; Repaired: {$repaired}; Errors: {$errors}");
        if (! empty($missing)) {
            $uniqueMissing = array_values(array_unique($missing));
            $this->line('Unique missing files ('.count($uniqueMissing).') sample: '.implode(', ', array_slice($uniqueMissing, 0, 10)).(count($uniqueMissing) > 10 ? ' ...' : ''));
            if ($exportMissing) {
                $csvPath = $exportMissing;
                if (!str_contains($csvPath, DIRECTORY_SEPARATOR)) {
                    $csvPath = base_path($csvPath);
                }
                $fh = fopen($csvPath, 'w');
                if ($fh) {
                    fputcsv($fh, ['song_id','stored_song_file']);
                    $lookup = [];
                    $query = Song::query()->whereIn('song_file', $uniqueMissing)->select(['id','song_file'])->get();
                    foreach ($query as $row) {
                        $lookup[$row->id] = $row->song_file;
                    }
                    foreach ($lookup as $id => $file) {
                        fputcsv($fh, [$id, $file]);
                    }
                    fclose($fh);
                    $this->info('Missing file list exported to: '.$csvPath);
                } else {
                    $this->error('Unable to write export file: '.$csvPath);
                }
            }
        }

        return $errors ? self::FAILURE : self::SUCCESS;
    }
}
