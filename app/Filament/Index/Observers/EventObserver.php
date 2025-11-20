<?php
namespace App\Filament\Index\Observers;

use App\Filament\Index\Mail\Events\Events\ModelCreated;
use App\Filament\Index\Mail\Events\Events\ModelDeleted;
use App\Filament\Index\Mail\Events\Events\ModelUpdated;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\SafeMailer;

class EventObserver
{
    public function created(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelCreated($event), 'event.created');
        }

        Log::info('Model created email sent to admins.');
    }

    public function updated(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelUpdated($event), 'event.updated');
        }

        Log::info('Model created email sent to admins.');
    }

    public function deleted(Event $event): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelDeleted($event), 'event.deleted');
        }

        Log::info('Model created email sent to admins.');
    }

}
