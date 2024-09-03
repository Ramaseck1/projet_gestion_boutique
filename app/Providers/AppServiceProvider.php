<?php

namespace App\Providers;

use App\Repositories\ClientRepositoryImpl;
use Illuminate\Support\ServiceProvider;
use App\Services\ClientService;
use App\Services\ClientServiceImpl;
use Laravel\Passport\Bridge\ClientRepository;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('ClientService'::class, ClientServiceImpl::class);
        $this->app->singleton('ClientRepository'::class, ClientRepositoryImpl::class);

        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
