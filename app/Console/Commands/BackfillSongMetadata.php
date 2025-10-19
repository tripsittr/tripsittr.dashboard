<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\Audio\AudioMetadataExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackfillSongMetadata extends Command
{
    protected $signature = 'songs:backfill-metadata {--waveform : Also generate waveform data if possible} {--bars=400 : Number of waveform bars} {--force : Re-extract even if raw_metadata exists} {--repair-paths : Attempt to repair missing file paths by scanning storage} {--export-missing= : Write list of missing file references to this path (CSV)}';

    protected $description = 'Extract extended audio metadata (and optionally waveform) for all songs';

    public function handle(AudioMetadataExtractor $extractor): int
    {
        $query = Song::query();
        $total = $query->count();
        $this->info("Processing {$total} songs...");

        $waveform = (bool)$this->option('waveform');
        $bars = (int)$this->option('bars');
        $force = (bool)$this->option('force');
    $repair = (bool)$this->option('repair-paths');
    $exportMissing = $this->option('export-missing');

        // Build an index of available audio files for path repair (basename => [relative paths]) if requested.
        $fileIndex = [];
        if ($repair) {
            $this->info('Building file index for repair...');
            $root = storage_path('app/public');
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
            $allowedExt = ['mp3','wav','flac','ogg','m4a','webm'];
            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if (!$file->isFile()) { continue; }
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $allowedExt, true)) { continue; }
                $basename = $file->getFilename();
                $relative = ltrim(str_replace($root, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                $fileIndex[$basename][] = $relative; // may be multiple
            }
            $this->info('Indexed '.count($fileIndex).' audio basenames.');
        }

        $processed = 0; $updated = 0; $skipped = 0; $errors = 0; $repaired = 0; $missing = [];

        $query->chunk(100, function ($songs) use (&$processed, &$updated, &$skipped, &$errors, &$repaired, &$missing, $extractor, $waveform, $bars, $force, $repair, $fileIndex) {
            foreach ($songs as $song) {
                $processed++;
                $relative = $song->song_file;
                if (!$relative) { $skipped++; continue; }
                $candidatePaths = [
                    storage_path('app/public/'.$relative),
                    public_path('storage/'.$relative), // symlinked public path
                    storage_path('app/private/'.$relative), // local disk variant
                ];
                $path = null;
                foreach ($candidatePaths as $c) {
                    if (is_file($c)) { $path = $c; break; }
                }
                if (!$path) {
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
                                $relative = $newRel;
                                $path = storage_path('app/public/'.$newRel);
                                $repaired++;
                            } else {
                                $this->warn("Ambiguous repair for {$relative} (".count($matches)." candidates)");
                            }
                        }
                    }
                }
                if (!$path || !is_file($path)) { $this->warn("Missing file: {$relative}"); $missing[] = $relative; $skipped++; continue; }
                if (!$force && $song->raw_metadata) { $skipped++; continue; }

                try {
                    $data = $extractor->extract($path);
                    $core = $data['core'];
                    $summary = $data['tag_summary'];

                    // Assign fields if they exist on the model
                    $assign = [
                        'duration' => $core['duration_seconds'] ?? null,
                        'bitrate' => $core['bitrate_kbps'] ?? null,
                        'bitrate_mode' => $core['bitrate_mode'] ?? null,
                        'sample_rate' => $core['sample_rate'] ?? null,
                        'codec' => $core['codec'] ?? null,
                        'format' => $core['format'] ?? null,
                        'channels' => $core['channels'] ?? null,
                        'file_size' => isset($core['filesize']) ? round($core['filesize'] / 1048576, 2) : null,
                        'mime_type' => $core['mime_type'] ?? null,
                        'track_number' => $core['track_number'] ?? null,
                        'track_total' => $core['track_total'] ?? null,
                        'disc_number' => $core['disc_number'] ?? null,
                        'disc_total' => $core['disc_total'] ?? null,
                        'replay_gain_track' => $data['replay_gain']['track_gain'] ?? null,
                        'replay_gain_album' => $data['replay_gain']['album_gain'] ?? null,
                        'md5_file' => $core['md5_file'] ?? null,
                        'raw_metadata' => $data['raw'] ?? null,
                    ];

                    foreach ($assign as $k => $v) {
                        if (in_array($k, $song->getFillable())) {
                            $song->{$k} = $v;
                        }
                    }

                    if ($waveform) {
                        $wave = $extractor->generateWaveformData($path, $bars);
                        if (!empty($wave)) {
                            $song->waveform_data = $wave; // will cast to array on model
                        }
                    }

                    try {
                        $song->save();
                    } catch (\JsonException $je) {
                        // Remove raw_metadata if still causing issues and retry once.
                        $this->warn("JSON encoding issue for song ID {$song->id}, stripping raw_metadata and retrying.");
                        $song->raw_metadata = null;
                        $song->save();
                    }
                    $updated++;
                } catch (\Throwable $e) {
                    $errors++;
                    $this->error("Error processing song ID {$song->id}: " . $e->getMessage());
                }
            }
        });

        $this->line("Processed: {$processed}; Updated: {$updated}; Skipped: {$skipped}; Repaired: {$repaired}; Errors: {$errors}");
        if (!empty($missing)) {
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
                    // Build map song_id => file for each missing (dedupe by song_id)
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
