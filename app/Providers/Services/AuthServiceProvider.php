<?php

namespace App\Providers\Services;

use App\Domains\Auth\AuthRepositoryInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(AuthServiceInterface::class, function ($app) {
            return new AuthService(
                $app->make(AuthRepositoryInterface::class),
            );
        });
    }
}
