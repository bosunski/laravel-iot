<?php

namespace Xeviant\LaravelIot\Foundation;

use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;

class MQTTClient extends ReactMqttClient implements MQTTClientInterface
{
}
