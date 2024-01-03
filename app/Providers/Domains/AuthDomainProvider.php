<?php

namespace App\Providers\Domains;

use App\Domains\Auth\AuthRepository;
use App\Domains\Auth\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class AuthDomainProvider extends ServiceProvider
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
        $this->app->bind(AuthRepositoryInterface::class, function($app) {
            return new AuthRepository(new User());
        });
    }
}
