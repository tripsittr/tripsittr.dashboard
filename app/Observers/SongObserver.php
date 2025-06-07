<?php

namespace App\Observers;

use App\Mail\Events\Songs\ModelCreated;
use App\Mail\Events\Songs\ModelDeleted;
use App\Mail\Events\Songs\ModelUpdated;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SongObserver
{
    public function created(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelCreated($song));
        }

        Log::info('Model created email sent to admins.');
    }

    public function updated(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelUpdated($song));
        }

        Log::info('Model created email sent to admins.');
    }

    public function deleted(Song $song): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelDeleted($song));
        }

        Log::info('Model created email sent to admins.');
    }
}
