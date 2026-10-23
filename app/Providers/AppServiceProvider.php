<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

        RateLimiter::for('registration', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(10)->by($request->ip()),
            ];
        });

        RateLimiter::for('ai', function (Request $request) {
            $key = 'user:' . ($request->user()?->getAuthIdentifier() ?? $request->ip());

            return [
                Limit::perMinute(4)->by($key),
                Limit::perHour(30)->by($key),
            ];
        });

        RateLimiter::for('comments', function (Request $request) {
            return Limit::perMinute(30)->by(
                'user:' . ($request->user()?->getAuthIdentifier() ?? $request->ip())
            );
        });

        RateLimiter::for('favorites', function (Request $request) {
            return Limit::perMinute(60)->by(
                'user:' . ($request->user()?->getAuthIdentifier() ?? $request->ip())
            );
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
