<?php

namespace App\Observers;

use App\Notifications\AlbumActivityNotification;
use App\Services\LogActivity;
use App\Models\Action;
use App\Models\Album;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SafeMailer;

class AlbumObserver
{
    public function created(Album $model): void
    {
        $action_type = 'create_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        $action = Action::where('action_type', $action_type)->first();
        $actionLabel = $action?->action_title ?? $action_type;
        foreach ($admins as $admin) {
            $admin->notify(new AlbumActivityNotification($model, 'created'));
        }
        LogActivity::record('album.created','Album',$model->id,[],Filament::getTenant()?->id);
    }

    public function updated(Album $model): void
    {
        $action_type = 'update_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        $action = Action::where('action_type', $action_type)->first();
        $actionLabel = $action?->action_title ?? $action_type;
        foreach ($admins as $admin) {
            $admin->notify(new AlbumActivityNotification($model, 'updated'));
        }
        LogActivity::record('album.updated','Album',$model->id,$model->getChanges(),Filament::getTenant()?->id);
    }

    public function deleted(Album $model): void
    {
        $action_type = 'delete_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        $action = Action::where('action_type', $action_type)->first();
        $actionLabel = $action?->action_title ?? $action_type;
        foreach ($admins as $admin) {
            $admin->notify(new AlbumActivityNotification($model, 'deleted'));
        }
        LogActivity::record('album.deleted','Album',$model->id,[],Filament::getTenant()?->id);

        Log::info('Model created email sent to admins.');
    }

    public function restored(User $user): void
    {
        //
    }

    public function forceDeleted(User $user): void
    {
        //
    }
}
