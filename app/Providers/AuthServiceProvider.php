<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        $this->registerPolicies();

        /* 
        admin -> define el can usado para mostrar los items del sidebar para 
        la vista de adminitrador de aplicacion
        */
        Gate::define('admin', function ($user) {
            return $user->rol === 'A';
        });

        /* 
        admin.rest -> define el can usado para mostrar los items del sidebar para 
        la vista de adminitrador de restaurante
        */
        Gate::define('admin.rest', function ($user) {
            return $user->rol === 'R';
        });
    }
}
