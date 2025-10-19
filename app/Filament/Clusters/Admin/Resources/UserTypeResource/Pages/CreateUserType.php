<?php

namespace App\Filament\Clusters\Admin\Resources\UserTypeResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserType extends CreateRecord
{
    protected static string $resource = UserTypeResource::class;
}
