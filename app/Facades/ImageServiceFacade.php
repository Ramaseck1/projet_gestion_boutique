<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\ImageService
 */
class ImageServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ImageService'; // Doit correspondre au nom utilisé dans le ServiceProvider
    }
}
