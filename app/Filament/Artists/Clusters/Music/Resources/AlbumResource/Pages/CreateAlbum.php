<?php

namespace App\Filament\Artists\Clusters\Music\Resources\AlbumResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;
}
