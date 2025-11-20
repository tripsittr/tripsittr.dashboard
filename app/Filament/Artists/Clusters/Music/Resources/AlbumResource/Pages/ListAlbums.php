<?php

namespace App\Filament\Artists\Clusters\Music\Resources\AlbumResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlbums extends ListRecords {
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
