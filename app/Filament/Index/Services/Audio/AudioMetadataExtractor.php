<?php
namespace App\Filament\Index\Services\Audio;

use getID3;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class AudioMetadataExtractor
{
    protected getID3 $analyzer;

    public function __construct()
    {
        $this->analyzer = new getID3();
    }

    /**
     * Extract rich metadata + safe raw structure.
     * Returns array with keys: core, tags, technical, replay_gain, hashes, images(meta only), raw
     */
    public function extract(string $absolutePath): array
    {
        $info = $this->analyzer->analyze($absolutePath);

        $raw = $this->sanitizeRaw($info);

        $audio = $info['audio'] ?? [];
        $tags  = $info['tags'] ?? [];
        $comments = $info['comments'] ?? [];

        // Track / disc parsing
        [$trackNo, $trackTotal] = $this->splitFraction($tags, ['id3v2.track_number','id3v2.track','id3v1.track']);
        [$discNo, $discTotal]   = $this->splitFraction($tags, ['id3v2.part_of_a_set','id3v2.disc_number','id3v2.disc']);

        $replayGain = [
            'track_gain' => Arr::get($info, 'replay_gain.track.adjustment', $this->firstTag($tags, ['id3v2.replaygain_track_gain'])),
            'album_gain' => Arr::get($info, 'replay_gain.album.adjustment', $this->firstTag($tags, ['id3v2.replaygain_album_gain'])),
        ];

        $core = [
            'duration_seconds' => Arr::get($info, 'playtime_seconds'),
            'duration_formatted' => Arr::get($info, 'playtime_string'),
            'bitrate' => Arr::get($info, 'bitrate'),
            'bitrate_kbps' => ($b = Arr::get($info, 'bitrate')) ? round($b / 1000) : null,
            'bitrate_mode' => Arr::get($audio, 'bitrate_mode'),
            'sample_rate' => Arr::get($audio, 'sample_rate'),
            'channels' => Arr::get($audio, 'channels'),
            'lossless' => Arr::get($audio, 'lossless'),
            'codec' => Arr::get($audio, 'codec') ?? Arr::get($audio, 'dataformat'),
            'dataformat' => Arr::get($audio, 'dataformat'),
            'format' => Arr::get($info, 'fileformat'),
            'compression_ratio' => Arr::get($audio, 'compression_ratio'),
            'encoder' => Arr::get($audio, 'encoder'),
            'encoder_options' => Arr::get($audio, 'encoder_options'),
            'md5_data' => Arr::get($info, 'md5_data'),
            'md5_file' => Arr::get($info, 'md5_file'),
            'sha1_file' => Arr::get($info, 'sha1_file'),
            'filesize' => Arr::get($info, 'filesize'),
            'mime_type' => Arr::get($info, 'mime_type'),
            'track_number' => $trackNo,
            'track_total' => $trackTotal,
            'disc_number' => $discNo,
            'disc_total' => $discTotal,
        ];

        $tagSummary = [
            'title' => $this->firstTag($tags, ['id3v2.title','id3v1.title','vorbiscomment.title','quicktime.title']),
            'artist' => $this->firstTag($tags, ['id3v2.artist','id3v1.artist','vorbiscomment.artist','quicktime.artist']),
            'album' => $this->firstTag($tags, ['id3v2.album','id3v1.album','vorbiscomment.album']),
            'album_artist' => $this->firstTag($tags, ['id3v2.band','id3v2.album_artist','vorbiscomment.albumartist']),
            'genre' => $this->firstTag($tags, ['id3v2.genre','id3v1.genre','vorbiscomment.genre']),
            'year' => $this->firstTag($tags, ['id3v2.year','id3v1.year']),
            'bpm' => $this->firstTag($tags, ['id3v2.bpm','vorbiscomment.bpm']),
            'mood' => $this->firstTag($tags, ['id3v2.mood']),
            'key' => $this->firstTag($tags, ['id3v2.initial_key']),
            'isrc' => $this->firstTag($tags, ['id3v2.isrc','id3v2.tsrc']),
            'copyright' => $this->firstTag($tags, ['id3v2.copyright_message']),
            'composer' => $this->firstTag($tags, ['id3v2.composer','vorbiscomment.composer']),
            'publisher' => $this->firstTag($tags, ['id3v2.publisher']),
            'language' => $this->firstTag($tags, ['id3v2.language']),
            'grouping' => $this->firstTag($tags, ['id3v2.grouping']),
            'subtitle' => $this->firstTag($tags, ['id3v2.subtitle']),
            'comment' => $this->firstTag($tags, ['id3v2.comment','vorbiscomment.comment']),
            'lyrics' => $this->firstTag($tags, ['id3v2.unsychronized_lyric','id3v2.unsynchronised_lyric']),
        ];

        $images = $this->extractImageMeta($info);

        return [
            'core' => $core,
            'tags' => $tags,
            'tag_summary' => $tagSummary,
            'technical' => [
                'audio' => $audio,
                'avdataoffset' => Arr::get($info, 'avdataoffset'),
                'avdataend' => Arr::get($info, 'avdataend'),
                'filesize' => Arr::get($info, 'filesize'),
            ],
            'replay_gain' => $replayGain,
            'images' => $images,
            'raw' => $raw,
        ];
    }

    public function generateWaveformData(string $absolutePath, int $bars = 500): array
    {
        // Requirements: ffmpeg installed & accessible.
        if (!is_file($absolutePath)) {
            return [];
        }
        $bars = max(50, min(2000, $bars));

        // Use ffmpeg to output raw mono 8-bit PCM at a reduced sample rate for speed.
        //  -ac 1 : mono
        //  -ar 4000 : 4kHz sample rate (enough for amplitude shape)
        //  -f u8 : unsigned 8-bit samples (0-255)
        $tmpFile = tempnam(sys_get_temp_dir(), 'wfpcm_');
        if ($tmpFile === false) {
            return [];
        }
        $cmd = sprintf('ffmpeg -nostdin -v error -i %s -ac 1 -ar 4000 -f u8 -y %s', escapeshellarg($absolutePath), escapeshellarg($tmpFile));
        exec($cmd, $out, $code);
        if ($code !== 0 || !is_file($tmpFile)) {
            @unlink($tmpFile);
            return [];
        }
        $data = file_get_contents($tmpFile);
        @unlink($tmpFile);
        if ($data === false || $data === '') {
            return [];
        }
        $length = strlen($data);
        if ($length === 0) {
            return [];
        }
        $samplesPerBar = $length / $bars;
        $wave = [];
        for ($i = 0; $i < $bars; $i++) {
            $start = (int) floor($i * $samplesPerBar);
            $end = (int) floor(($i + 1) * $samplesPerBar);
            if ($end <= $start) {
                $end = $start + 1;
            }
            $sum = 0; $count = 0;
            for ($j = $start; $j < $end && $j < $length; $j++) {
                // Convert unsigned 8-bit (0-255) to normalized amplitude 0..1 centered.
                $val = ord($data[$j]);
                // Convert to centered float (-1..1)
                $centered = ($val - 128) / 128; // -1 .. ~0.992
                $sum += $centered * $centered; // RMS energy
                $count++;
            }
            $rms = $count ? sqrt($sum / $count) : 0;
            $wave[] = round($rms, 4);
        }
        return $wave;
    }

    private function sanitizeRaw(array $info): array
    {
        // Avoid storing large binary blobs (strip picture data)
        if (isset($info['comments']['picture']) && is_array($info['comments']['picture'])) {
            foreach ($info['comments']['picture'] as $i => $pic) {
                if (isset($pic['data'])) {
                    $info['comments']['picture'][$i]['data_length'] = strlen($pic['data']);
                    unset($info['comments']['picture'][$i]['data']);
                }
            }
        }
        // Recursively ensure all strings are valid UTF-8 so JSON encoding of raw_metadata never fails.
        return $this->utf8ize($info);
    }

    private function extractImageMeta(array $info): array
    {
        $out = [];
        $pictures = $info['comments']['picture'] ?? [];
        foreach ($pictures as $pic) {
            $out[] = [
                'mime' => $pic['image_mime'] ?? ($pic['mime'] ?? null),
                'description' => $pic['description'] ?? null,
                'width' => $pic['image_width'] ?? null,
                'height' => $pic['image_height'] ?? null,
                'bits' => $pic['bits_per_sample'] ?? null,
                'data_length' => isset($pic['data']) ? strlen($pic['data']) : ($pic['data_length'] ?? null),
            ];
        }
        return $out;
    }

    private function firstTag(array $tags, array $candidates): mixed
    {
        foreach ($candidates as $path) {
            $value = Arr::get($tags, $path);
            if (is_array($value)) {
                $value = $value[0] ?? null;
            }
            if ($value !== null && $value !== '') {
                return $value;
            }
        }
        return null;
    }

    private function splitFraction(array $tags, array $paths): array
    {
        $raw = $this->firstTag($tags, $paths);
        if (!is_string($raw)) {
            return [null, null];
        }
        if (preg_match('/^(\d+)(?:\/(\d+))?$/', trim($raw), $m)) {
            return [isset($m[1]) ? (int)$m[1] : null, isset($m[2]) ? (int)$m[2] : null];
        }
        return [null, null];
    }

    /**
     * Recursively convert all string values to valid UTF-8, stripping / substituting invalid byte sequences.
     * Strategy:
     *  - If string already valid UTF-8 -> leave as-is
     *  - Else attempt common legacy encodings (ISO-8859-1, Windows-1252)
     *  - Fallback: replace invalid bytes using iconv with //IGNORE, finally remove any remaining control chars except tab/newline.
     */
    private function utf8ize(mixed $value): mixed
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                // Preserve keys; sanitize key if it's a string with invalid encoding
                if (is_string($k) && !mb_check_encoding($k, 'UTF-8')) {
                    $k = $this->fixEncoding($k);
                }
                $out[$k] = $this->utf8ize($v);
            }
            return $out;
        }
        if (is_string($value)) {
            return $this->fixEncoding($value);
        }
        // Scalars (int/float/bool/null) unchanged
        return $value;
    }

    private function fixEncoding(string $s): string
    {
        if ($s === '') { return $s; }
        if (mb_check_encoding($s, 'UTF-8')) { return $this->stripControlChars($s); }
        // Try ISO-8859-1 then Windows-1252
        foreach (['ISO-8859-1', 'Windows-1252'] as $enc) {
            $converted = @iconv($enc, 'UTF-8//IGNORE', $s);
            if ($converted !== false && $converted !== '') {
                if (mb_check_encoding($converted, 'UTF-8')) {
                    return $this->stripControlChars($converted);
                }
            }
        }
        // Final fallback: force UTF-8 by removing invalid bytes.
        $forced = @iconv('UTF-8', 'UTF-8//IGNORE', $s);
        if ($forced === false) {
            $forced = utf8_encode($s); // last resort
        }
        return $this->stripControlChars($forced);
    }

    private function stripControlChars(string $s): string
    {
        // Allow tab (9), newline (10), carriage return (13); remove other C0 controls.
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
    }
}
