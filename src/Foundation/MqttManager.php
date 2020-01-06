<?php

namespace Xeviant\LaravelIot\Foundation;

use Symfony\Component\Routing\Route;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;

class MqttManager
{
    /**
     * @var MQTTClientInterface
     */
    private $client;
    /**
     * @var MqttRouter
     */
    private $router;

    public function __construct(MQTTClientInterface $client, MqttRouter $router)
    {
        $this->client = $client;
        $this->router = $router;
    }

    public function subscribe(string $topic, $handler): Route
    {
        return $this->router->topic($topic, $handler);
    }

    public function publish(string $topic, string $payload = ""): void
    {
        resolve('mqtt.publisher')->publish($topic, $payload);
    }
}
