<?php

namespace App\Observers;

use App\Mail\Events\Songs\ModelCreated;
use App\Mail\Events\Songs\ModelDeleted;
use App\Mail\Events\Songs\ModelUpdated;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\SafeMailer;

class SongObserver
{
    public function created(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelCreated($song), 'song.created');
        }

        Log::info('Model created email sent to admins.');
    }

    public function updated(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelUpdated($song), 'song.updated');
        }

        Log::info('Model created email sent to admins.');
    }

    public function deleted(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelDeleted($song), 'song.deleted');
        }

        Log::info('Model created email sent to admins.');
    }
}
