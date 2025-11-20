<?php

namespace App\Filament\Artists\Clusters\Music\Resources\AlbumResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\AlbumResource;
use App\Filament\Artists\Clusters\Music\Resources\TracksResource\Widgets\TracksTableWidget;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Resources\Pages\ViewRecord;

class ViewAlbum extends ViewRecord
{
    protected static string $resource = AlbumResource::class;

    use HasRecentHistoryRecorder;

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Index\Widgets\AlbumSongsTileWidget::make([
                'album' => $this->record,
            ]),
            TracksTableWidget::make([
                'albumId' => $this->record->id,
            ]),
        ];
    }
}
