<?php

namespace App\Filament\Clusters\Admin\Resources\UserActionsResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserActionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserActions extends CreateRecord
{
    protected static string $resource = UserActionsResource::class;
}
