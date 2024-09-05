<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class QrCodeServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'QrCodeService';
    }
}
