<?php

namespace App\Filament\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Clusters\Music\Resources\TracksResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListTracks extends ListRecords {
    protected static string $resource = TracksResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
