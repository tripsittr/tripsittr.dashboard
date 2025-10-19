<?php

namespace App\Filament\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Clusters\Music\Resources\TracksResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrack extends CreateRecord {
    protected static string $resource = TracksResource::class;
}
