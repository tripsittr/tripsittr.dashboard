<?php

namespace App\Filament\Clusters\Music\Resources\TracksResource\Pages;

use App\Filament\Clusters\Music\Resources\TracksResource;
use Filament\Resources\Pages\ViewRecord;
use getID3;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewTrack extends ViewRecord {
    use HasRecentHistoryRecorder;

    protected static string $resource = TracksResource::class;
}
