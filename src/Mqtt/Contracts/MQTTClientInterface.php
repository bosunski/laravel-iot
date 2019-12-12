<?php

namespace Xeviant\LaravelIot\Mqtt\Contracts;

use BinSoul\Net\Mqtt\Connection;
use BinSoul\Net\Mqtt\Subscription;
use Evenement\EventEmitterInterface;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\PromiseInterface;

interface MQTTClientInterface extends EventEmitterInterface
{
    public function getHost();

    public function getPort();

    /**
     * @param $host
     * @param int $port
     * @param Connection|null $connection
     * @param int $timeout
     * @return ExtendedPromiseInterface
     */
    public function connect(string $host, int $port = 1883, ?Connection $connection = null, int $timeout = 5): PromiseInterface;
    public function disconnect();
    public function subscribe(Subscription $subscription);
}
