<?php

namespace Xeviant\LaravelIot\Foundation;

use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use BinSoul\Net\Mqtt\DefaultMessage;

class MqttPublisher
{
    /**
     * @var MQTTClientInterface|MQTTClient
     */
    private $client;

    public function __construct(MQTTClientInterface $client)
    {
        $this->client = $client;
    }

    public function publish(string $topic, string $payload = "")
    {
        return $this->client->publish(new DefaultMessage($topic, $payload))->then(function ($done) use ($topic, $payload) {
            echo "Published:=> $payload on $topic\n";
        });
    }
}
