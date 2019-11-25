<?php


namespace Xeviant\LaravelIot\Facade;


use Illuminate\Support\Facades\Facade;

class Mqtt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Xeviant\LaravelIot\Foundation\MqttRouter::class;
    }
}
