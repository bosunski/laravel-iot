<?php

namespace Xeviant\LaravelIot\Foundation;

use Symfony\Component\Routing\Route;

class MqttManager
{
    public function subscribe(string $topic, $handler): Route
    {
        return resolve('mqtt.router')->topic($topic, $handler);
    }

    public function publish(string $topic, string $payload = ""): void
    {
        resolve('mqtt.publisher')->publish($topic, $payload);
    }
}
