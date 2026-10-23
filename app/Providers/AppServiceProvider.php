<?php

namespace App\Providers;

use App\Listeners\RecordAuthenticationEvent;
use App\Models\User;
use App\Observers\ListingMediaObserver;
use App\Observers\UserObserver;
use App\Support\ListingMediaUrlGenerator;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        config(['media-library.url_generator' => ListingMediaUrlGenerator::class]);
        Password::defaults(fn () => Password::min(12)->letters()->mixedCase()->numbers());
        RateLimiter::for('listing-reports', fn (Request $request): array => [
            Limit::perHour(3)->by('hour:'.hash('sha256', $request->ip())),
            Limit::perDay(10)->by('day:'.hash('sha256', $request->ip())),
        ]);
        RateLimiter::for('submissions', fn (Request $request): array => [
            Limit::perHour(3)->by('hour:'.hash('sha256', $request->ip())),
            Limit::perDay(10)->by('day:'.hash('sha256', $request->ip())),
        ]);
        User::observe(UserObserver::class);
        Media::observe(ListingMediaObserver::class);
        Event::listen(Login::class, [RecordAuthenticationEvent::class, 'login']);
        Event::listen(Logout::class, [RecordAuthenticationEvent::class, 'logout']);
        Event::listen(Failed::class, [RecordAuthenticationEvent::class, 'failed']);
    }
}
