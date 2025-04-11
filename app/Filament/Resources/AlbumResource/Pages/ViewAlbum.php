<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use Filament\Resources\Pages\ViewRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class ViewAlbum extends ViewRecord {
    protected static string $resource = AlbumResource::class;

    use HasRecentHistoryRecorder;
}
