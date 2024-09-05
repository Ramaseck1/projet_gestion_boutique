<?php

namespace App\Providers;

use App\Repositories\ClientRepositoryImpl;
use App\Services\PhotoServiceInterface;
use App\Services\PhotoServiceImpl;
use Illuminate\Support\ServiceProvider;
use App\Services\ClientService;
use App\Services\ClientServiceImpl;
use Laravel\Passport\Bridge\ClientRepository;
use App\Services\Base64ImageService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('ClientService'::class, ClientServiceImpl::class);
        $this->app->singleton('ClientRepository'::class, ClientRepositoryImpl::class);
        $this->app->singleton('uploadservice', function ($app) {
            return new \App\Services\UploadService();
        });

        //qrcode
        $this->app->singleton('QrCodeService', function ($app) {
            return new \App\Services\UploadService();
        });

        //base64
        $this->app->singleton('ImageService', function ($app) {
            return new \App\Services\UploadService();
        });
        
        $this->app->bind(PhotoServiceInterface::class, PhotoServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
