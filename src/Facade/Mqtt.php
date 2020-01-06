<?php

namespace Xeviant\LaravelIot\Facade;

use Illuminate\Support\Facades\Facade;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;

class Mqtt extends Facade
{
    public static function client(): MQTTClientInterface
    {
        return app()->get(MQTTClientInterface::class);
    }

    protected static function getFacadeAccessor()
    {
        return 'xeviant.mqtt';
    }
}
