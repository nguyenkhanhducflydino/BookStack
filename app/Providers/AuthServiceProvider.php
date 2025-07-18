<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Password::defaults(fn() => Password::min(8));
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton('users.default', function () {
            return User::query()->where('system_name', '=', 'public')->first();
        });
    }
}
