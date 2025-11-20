<?php

namespace App\Filament\Artists\Clusters\Music\Resources\AlbumResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class EditAlbum extends EditRecord {
    protected static string $resource = AlbumResource::class;

    use HasRecentHistoryRecorder;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
