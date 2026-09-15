<?php

namespace App\Providers;

use App\Events\UserBlocked;
use App\Listeners\LogoutBlockedUser;
use App\Listeners\NotifyAdminsListener;
use App\Models\Product;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);

        Event::listen(
            UserBlocked::class,
            LogoutBlockedUser::class
        );

        Event::listen(
            UserBlocked::class,
            NotifyAdminsListener::class
        );
    }
}
