<?php


namespace Bosunski\LaravelIot\Foundation;


use Bosunski\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;

class MQTTClient extends ReactMqttClient implements MQTTClientInterface
{
}
