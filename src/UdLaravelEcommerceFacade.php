<?php

namespace Unusualdope\LaravelEcommerce;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Unusualdope\LaravelEcommerce\Skeleton\SkeletonClass
 */
class UdLaravelEcommerceFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ud-laravel-ecommerce';
    }
}
