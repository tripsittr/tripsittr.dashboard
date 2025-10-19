<?php

namespace App\Filament\Clusters\Admin\Resources\TeamResource\Pages;

use App\Filament\Clusters\Admin\Resources\TeamResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\Log;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        $record = static::getModel()::withTrashed()->findOrFail($key);
        Log::debug('EditTeam resolveRecord', ['id'=>$key,'found'=>true]);
        return $record;
    }
}
