<?php

namespace App\Filament\Admin\Clusters\Admin\Resources\TeamResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\TeamResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getModel()::query()->withoutGlobalScopes();
    }
}
