<?php

namespace Kartovitskii\Laracolor\Facades;

use Illuminate\Support\Facades\Facade;

class Laracolor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laracolor';
    }
}