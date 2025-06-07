<?php

namespace App\Observers;

use App\Mail\Events\Events\ModelCreated;
use App\Mail\Events\Events\ModelDeleted;
use App\Mail\Events\Events\ModelUpdated;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventObserver
{
    public function created(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelCreated($event));
        }

        Log::info('Model created email sent to admins.');
    }

    public function updated(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelUpdated($event));
        }

        Log::info('Model created email sent to admins.');
    }

    public function deleted(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ModelDeleted($event));
        }

        Log::info('Model created email sent to admins.');
    }

}
