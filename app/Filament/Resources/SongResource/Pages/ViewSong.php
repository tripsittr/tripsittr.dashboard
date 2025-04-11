<?php

namespace App\Filament\Resources\SongResource\Pages;

use App\Filament\Resources\SongResource;
use Filament\Resources\Pages\ViewRecord;
use getID3;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class ViewSong extends ViewRecord {
    use HasRecentHistoryRecorder;

    protected static string $resource = SongResource::class;
}
