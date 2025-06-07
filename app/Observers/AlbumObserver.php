<?php

namespace App\Observers;

use App\Mail\Events\Albums\ModelCreated;
use App\Mail\Events\Albums\ModelDeleted;
use App\Mail\Events\Albums\ModelUpdated;
use App\Mail\Events\UserAction;
use App\Models\Action;
use App\Models\Album;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlbumObserver
{
    public function created(Album $model): void
    {
        $action_type = 'create_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new UserAction($model, Action::All()->where('action_type', $action_type), Auth::user()->id, Filament::getTenant()->id));
        }
    }

    public function updated(Album $model): void
    {
        $action_type = 'update_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new UserAction($model, Action::All()->where('action_type', $action_type), Auth::user()->id, Filament::getTenant()->id));
        }
    }

    public function deleted(Album $model): void
    {
        $action_type = 'delete_album';

        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new UserAction($model, Action::All()->where('action_type', $action_type), Auth::user()->id, Filament::getTenant()->id));
        }

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
