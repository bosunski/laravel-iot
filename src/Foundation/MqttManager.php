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

    public function topic(string $topic, $handler): Route
    {
        return $this->router->topic($topic, $handler);
    }
}
