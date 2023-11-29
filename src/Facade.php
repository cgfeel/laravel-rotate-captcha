<?php

namespace Levi\LaravelRotateCaptcha;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return Captcha::class;
    }
}
