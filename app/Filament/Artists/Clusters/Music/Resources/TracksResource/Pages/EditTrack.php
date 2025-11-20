<?php

namespace App\Filament\Artists\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\TracksResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class EditTrack extends EditRecord {
    use HasRecentHistoryRecorder;

    protected static string $resource = TracksResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
