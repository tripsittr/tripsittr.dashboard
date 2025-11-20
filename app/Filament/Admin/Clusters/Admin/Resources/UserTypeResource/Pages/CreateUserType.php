<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\UserTypeResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\UserTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserType extends CreateRecord
{
    protected static string $resource = UserTypeResource::class;
}
