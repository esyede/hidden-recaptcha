<?php

namespace Esyede\HiddenReCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

class HiddenReCaptcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }
}
