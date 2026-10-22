<?php

namespace App\Providers;

use App\Listeners\RecordAuthenticationEvent;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(fn () => Password::min(12)->letters()->mixedCase()->numbers());
        User::observe(UserObserver::class);
        Event::listen(Login::class, [RecordAuthenticationEvent::class, 'login']);
        Event::listen(Logout::class, [RecordAuthenticationEvent::class, 'logout']);
        Event::listen(Failed::class, [RecordAuthenticationEvent::class, 'failed']);
    }
}
