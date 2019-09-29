<?php

namespace Bosunski\LaravelIot\Mqtt\Contracts;

use BinSoul\Net\Mqtt\Connection;
use BinSoul\Net\Mqtt\Subscription;
use Evenement\EventEmitterInterface;
use React\Promise\ExtendedPromiseInterface;

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
    public function connect($host, $port = 1883, Connection $connection = null, $timeout = 5);
    public function subscribe(Subscription $subscription);
}
