<?php

namespace Bosunski\LaravelIot;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bosunski\LaravelIot\Skeleton\SkeletonClass
 */
class LaravelIotFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-iot';
    }
}
