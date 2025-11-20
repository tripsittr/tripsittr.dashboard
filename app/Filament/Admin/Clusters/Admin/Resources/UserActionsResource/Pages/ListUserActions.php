<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\UserActionsResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\UserActionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserActions extends ListRecords
{
    protected static string $resource = UserActionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
