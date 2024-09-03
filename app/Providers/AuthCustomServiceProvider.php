<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Auth\AuthenticationServiceInterface;
use App\Services\Auth\AuthentificationSanctum;
use App\Services\Auth\AuthentificationPassport;

class AuthCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(AuthenticationServiceInterface::class, function ($app) {
          /*   if (config('auth.defaults.guard') === 'sanctum') {
                return new AuthentificationSanctum();
            } else {
                return new AuthentificationPassport();
            } */
           return new AuthentificationPassport();
          
/*              return new AuthentificationSanctum();
 */
        });
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
