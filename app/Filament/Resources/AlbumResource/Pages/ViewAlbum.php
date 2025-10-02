<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Filament\Resources\SongResource\Widgets\SongsTableWidget;
use Filament\Resources\Pages\ViewRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class ViewAlbum extends ViewRecord
{
    protected static string $resource = AlbumResource::class;

    use HasRecentHistoryRecorder;

    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\AlbumSongsTileWidget::make([
                'album' => $this->record,
            ]),
            SongsTableWidget::make([
                'albumId' => $this->record->id,
            ]),
        ];
    }
}
