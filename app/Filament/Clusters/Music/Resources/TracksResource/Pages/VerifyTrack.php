<?php

namespace App\Filament\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Clusters\Music\Resources\TracksResource;
use App\Models\Song;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class VerifyTrack extends Page
{
    protected static string $resource = TracksResource::class;

    protected static string $view = 'filament.clusters.music.resources.tracks-resource.pages.verify-track';

    public ?Song $record = null;

    public array $checks = [];

    public function mount($record): void
    {
        $this->record = Song::findOrFail($record);
        $this->checks = $this->buildChecks($this->record);
    }

    private function buildChecks(Song $song): array
    {
        $checks = [];
        $checks['file_present'] = (bool) $song->resolveSongAbsolutePath();
        $checks['duration_set'] = (bool) ($song->duration && $song->duration > 0);
        $checks['artwork_present'] = (bool) ($song->artwork);
        $checks['title_present'] = (bool) ($song->title);
        $checks['genre_present'] = (bool) ($song->genre);
        $checks['primary_artists_present'] = !empty($song->primary_artists);
        $checks['release_date_set'] = (bool) ($song->release_date);
        $checks['isrc_present'] = (bool) ($song->isrc); // optional, but recommended

        return $checks;
    }
}
