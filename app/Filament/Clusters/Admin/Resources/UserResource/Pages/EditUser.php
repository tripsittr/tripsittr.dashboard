<?php

namespace App\Filament\Clusters\Admin\Resources\UserResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class EditUser extends EditRecord {
    use HasRecentHistoryRecorder;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
