<?php

namespace App\Filament\Artists\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Artists\Clusters\Music\Resources\TracksResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrack extends CreateRecord {
    protected static string $resource = TracksResource::class;
}
