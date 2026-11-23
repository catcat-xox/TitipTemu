<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Paginator::useBootstrapFive();

        Gate::define('admin', fn (User $user): bool => $user->isAdmin());
        Gate::define('user', fn (User $user): bool => $user->isUser());

        RateLimiter::for('auth', function (Request $request): Limit {
            return Limit::perMinute(10)->by(
                $request->ip().'|'.strtolower((string) $request->input('email')),
            );
        });
    }
}
