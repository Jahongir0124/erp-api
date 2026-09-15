<?php

namespace App\Observers;

use App\Enums\UserStatus;
use App\Events\UserBlocked;
use App\Jobs\UserBlockedJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }



    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {

        if ($user->wasChanged('status') && $user->status === UserStatus::BLOCKED) {
           
            event(
                new UserBlocked($user)
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
