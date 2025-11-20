<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\UserTypeResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\UserTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserTypes extends ListRecords
{
    protected static string $resource = UserTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
