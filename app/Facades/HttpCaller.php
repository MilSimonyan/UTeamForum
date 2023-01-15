<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $string)
 */
class HttpCaller extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'http_caller';
    }
}
