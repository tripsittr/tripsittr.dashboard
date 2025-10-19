<?php

namespace App\Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class AudioEntry extends Entry
{
    protected string $view = 'filament.components.infolists.audio-entry';

    // Core supplied data
    protected string|Closure|null $audioUrl = null;          // Explicit audio URL (overrides state)

    protected array|Closure|null $metadata = null;           // Precomputed metadata array

    // Player configuration (ported & merged from alternate component implementation)
    protected bool $autoplay = false;

    protected bool $showControls = true;

    protected bool $showTimeDisplay = true;

    protected bool $showVolumeControl = true;

    protected bool $showSpeedControl = true;

    protected float $defaultVolume = 1.0;

    protected float $defaultSpeed = 1.0;

    protected ?string $audioFormat = null;         // Hint for type attribute

    protected ?string $primaryColor = null;        // Tailwind color class or hex

    protected ?string $backgroundColor = null;     // Tailwind color class or hex

    public function audioUrl(string|Closure|null $audioUrl): static
    {
        $this->audioUrl = $audioUrl;

        return $this;
    }

    /** Enable or disable autoplay */
    public function autoplay(bool $autoplay = true): static
    {
        $this->autoplay = $autoplay;

        return $this;
    }

    /** Show or hide native controls */
    public function controls(bool $show = true): static
    {
        $this->showControls = $show;

        return $this;
    }

    

    /** Show or hide time display */
    public function timeDisplay(bool $show = true): static
    {
        $this->showTimeDisplay = $show;

        return $this;
    }

    /** Show or hide volume control */
    public function volumeControl(bool $show = true): static
    {
        $this->showVolumeControl = $show;

        return $this;
    }

    /** Show or hide speed (playbackRate) control */
    public function speedControl(bool $show = true): static
    {
        $this->showSpeedControl = $show;

        return $this;
    }

    /** Default volume (0..1) */
    public function defaultVolume(float $volume): static
    {
        $this->defaultVolume = max(0.0, min(1.0, $volume));

        return $this;
    }

    /** Default speed */
    public function defaultSpeed(float $speed): static
    {
        $this->defaultSpeed = $speed;

        return $this;
    }

    /** Format hint */
    public function format(string $format): static
    {
        $this->audioFormat = $format;

        return $this;
    }

    

    /** Primary color */
    public function primaryColor(string $color): static
    {
        $this->primaryColor = $color;

        return $this;
    }

    /** Background color */
    public function backgroundColor(string $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    

    public function metadata(array|Closure|null $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getAudioUrl(): ?string
    {
        $explicit = $this->audioUrl instanceof Closure ? $this->evaluate($this->audioUrl) : $this->audioUrl;
        if (is_string($explicit) && $explicit !== '') {
            return $explicit;
        }
        // Derive from state (assumed to be a storage path) if available
        $state = $this->getState();
        if (is_string($state) && $state !== '') {
            // If the stored path starts with legacy 'demo/' and file not present, attempt repair by basename search
            if (str_starts_with($state, 'demo/')) {
                $basename = basename($state);
                // Search common audio directories for this basename
                $searchRoots = [
                    'audio',
                    'songs',
                ];
                foreach ($searchRoots as $root) {
                    $files = Storage::files($root);
                    foreach ($files as $f) {
                        if (basename($f) === $basename) {
                            return Storage::url($f);
                        }
                    }
                }
            }
            try {
                return Storage::url($state);
            } catch (\Throwable $e) {
                try {
                    return Storage::url($state);
                } catch (\Throwable $e2) {
                    return $state; // final fallback
                }
            }
        }

        return null;
    }

    

    public function getMetadata(): ?array
    {
        $base = $this->evaluate($this->metadata) ?? [];
        $url = $this->getAudioUrl();
        if ($url && empty($base['url'])) {
            $base = array_merge([
                'url' => $url,
                'filename' => basename(parse_url($url, PHP_URL_PATH) ?? ''),
                'extension' => strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)),
            ], $base);
        }
        // Fallback: pull from record raw_metadata if not explicitly provided
        if (empty($base) && method_exists($this, 'getRecord')) {
            $record = $this->getRecord();
            if ($record && isset($record->raw_metadata) && is_array($record->raw_metadata)) {
                $summary = $record->raw_metadata['tag_summary'] ?? [];
                $core = $record->raw_metadata['core'] ?? [];
                $base = array_merge($core, $summary, ['url' => $url]);
            }
        }
        return $base ?: null;
    }

    public function getActions(): array
    {
        return [
            Action::make('viewMetadata')
                ->label('View Metadata')
                ->modalHeading('Song Metadata')
                ->modalContent(View::make('filament.components.infolists.audio-metadata', ['metadata' => $this->getMetadata()]))
                ->modalWidth('lg'),
        ];
    }

    public function viewMetadata()
    {
        // Logic to handle viewing metadata
    }

    /** Consolidated player config for the Blade view */
    public function getPlayerConfig(): array
    {
        return [
            'autoplay' => $this->autoplay,
            'showControls' => $this->showControls,
            'showTimeDisplay' => $this->showTimeDisplay,
            'showVolumeControl' => $this->showVolumeControl,
            'showSpeedControl' => $this->showSpeedControl,
            'defaultVolume' => $this->defaultVolume,
            'defaultSpeed' => $this->defaultSpeed,
            'audioFormat' => $this->audioFormat,
            'primaryColor' => $this->primaryColor,
            'backgroundColor' => $this->backgroundColor,
        ];
    }

    /** Basic validation of audio URL */
    public function isValidAudioUrl(?string $url): bool
    {
        if (! is_string($url) || $url === '') {
            return false;
        }
        // Accept full URLs, absolute paths, or relative storage paths. Only check extension.
        $path = parse_url($url, PHP_URL_PATH) ?: $url; // parse_url may return null for relative
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return $ext !== '' && in_array($ext, ['mp3','wav','ogg','flac','aac','m4a','wma']);
    }
}
