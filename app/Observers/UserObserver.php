<?php

namespace App\Observers;

use App\Mail\ModelCreated;
use App\Mail\ModelDeleted;
use App\Mail\ModelUpdated;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    public function created(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelCreated($user));
        }

        Log::info('Model created email sent to admins.');
    }

    public function updated(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        if (!empty(Filament::getTenant()->id)) {
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new ModelUpdated($user));
            }
        };

        Log::info('Model created email sent to admins.');
    }

    public function deleted(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelDeleted($user));
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
