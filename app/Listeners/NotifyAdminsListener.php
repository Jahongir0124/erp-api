<?php

namespace App\Listeners;

use App\Events\UserBlocked;
use App\Jobs\SendAdminNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;


class NotifyAdminsListener
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
        Log::info('Listener ishladi');
        SendAdminNotificationJob::dispatch(
            $event->user
        );
    }
}
