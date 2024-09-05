<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageService;


class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Enregistre le service ImageService dans le conteneur d'injection de dépendances
        $this->app->singleton('ImageService', function ($app) {
            return new ImageService();
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
