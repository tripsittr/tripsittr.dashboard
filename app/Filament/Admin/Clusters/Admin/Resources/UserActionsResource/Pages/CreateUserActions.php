<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\UserActionsResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\UserActionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserActions extends CreateRecord
{
    protected static string $resource = UserActionsResource::class;
}
