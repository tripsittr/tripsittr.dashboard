<?php

namespace App\Filament\Clusters\Admin\Resources\UserActionsResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserActionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserActions extends EditRecord
{
    protected static string $resource = UserActionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
