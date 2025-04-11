<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class EditInventory extends EditRecord {

    use HasRecentHistoryRecorder;
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
