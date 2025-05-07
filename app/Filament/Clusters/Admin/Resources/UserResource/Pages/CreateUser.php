<?php

namespace App\Filament\Clusters\Admin\Resources\UserResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
