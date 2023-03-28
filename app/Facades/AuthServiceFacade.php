<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;


class AuthServiceFacade extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'auth-service';
    }
}
