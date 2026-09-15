<?php

namespace App\Listeners;

use App\Events\UserBlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;




class LogoutBlockedUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserBlocked $event): void
    {
        Log::info(
            "Blocked user: {$event->user->id} {$event->user->name}"
        );

        $event->user->tokens()->delete();
    }
}
