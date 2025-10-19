<?php

namespace App\Observers;

use App\Mail\Events\Users\ModelCreated;
use App\Mail\Events\Users\ModelDeleted;
use App\Mail\Events\Users\ModelUpdated;
use App\Models\User;
use App\Services\SafeMailer;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        $actionType = 'create_user';
        $authUserId = Auth::id();
        $tenantId = optional(Filament::getTenant())->id;
        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelCreated($user, $actionType, $authUserId, $tenantId), 'user.created');
        }

        Log::info('User created email sent to admins.');
    }

    public function updated(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        $tenant = Filament::getTenant();
        if (! empty($tenant?->id)) {
            foreach ($admins as $admin) {
                SafeMailer::send($admin->email, new ModelUpdated($user), 'user.updated');
            }
        }

        Log::info('User updated email sent to admins.');
    }

    public function deleted(User $user): void
    {
        $admins = User::whereHas('teams', function ($query) {
            $query->where('type', 'Admin');
        })->orWhere('type', 'Admin')->get();

        foreach ($admins as $admin) {
            SafeMailer::send($admin->email, new ModelDeleted($user), 'user.deleted');
        }

        Log::info('User deleted email sent to admins.');
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
