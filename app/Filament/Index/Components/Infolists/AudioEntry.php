<?php

namespace App\Filament\Index\Components\Infolists;

use Filament\Infolists\Components\Entry;

class AudioEntry extends Entry
{
    protected string $view = 'filament.components.infolists.audio-entry';

    protected bool $autoplay = false;

    protected bool $showControls = true;

    protected bool $showTimeDisplay = true;

    protected bool $showVolumeControl = true;

    protected bool $showSpeedControl = true;

    protected float $defaultVolume = 1.0;

    protected float $defaultSpeed = 1.0;

    protected ?string $audioFormat = null;

    protected ?string $primaryColor = null;

    protected ?string $backgroundColor = null;

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    /**
     * Enable or disable autoplay
     */
    public function autoplay(bool $autoplay = true): static
    {
        $this->autoplay = $autoplay;

        return $this;
    }

    /**
     * Show or hide player controls
     */
    public function controls(bool $show = true): static
    {
        $this->showControls = $show;

        return $this;
    }

    /**
     * Show or hide time display
     */
    public function timeDisplay(bool $show = true): static
    {
        $this->showTimeDisplay = $show;

        return $this;
    }

    /**
     * Show or hide volume control
     */
    public function volumeControl(bool $show = true): static
    {
        $this->showVolumeControl = $show;

        return $this;
    }

    /**
     * Show or hide speed control
     */
    public function speedControl(bool $show = true): static
    {
        $this->showSpeedControl = $show;

        return $this;
    }

    /**
     * Set default volume (0.0 to 1.0)
     */
    public function defaultVolume(float $volume): static
    {
        $this->defaultVolume = max(0.0, min(1.0, $volume));

        return $this;
    }

    /**
     * Set default playback speed
     */
    public function defaultSpeed(float $speed): static
    {
        $this->defaultSpeed = $speed;

        return $this;
    }

    /**
     * Set audio format hint
     */
    public function format(string $format): static
    {
        $this->audioFormat = $format;

        return $this;
    }

    /**
     * Set primary color for the player
     */
    public function primaryColor(string $color): static
    {
        $this->primaryColor = $color;

        return $this;
    }

    /**
     * Set background color for the player
     */
    public function backgroundColor(string $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    /**
     * Get player configuration for the view
     */
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

    /**
     * Check if the audio URL is valid
     */
    public function isValidAudioUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        // Check if it's a valid URL
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check for common audio file extensions
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return in_array($extension, $audioExtensions);
    }

    /**
     * Format file size for display
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Get audio metadata if available
     */
    public function getAudioMetadata(): ?array
    {
        $url = $this->getState();

        if (! $this->isValidAudioUrl($url)) {
            return null;
        }

        // In a real implementation, you might want to extract actual metadata
        // For now, return basic info based on URL
        return [
            'url' => $url,
            'filename' => basename(parse_url($url, PHP_URL_PATH)),
            'extension' => strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)),
            'is_valid' => true,
        ];
    }
}
