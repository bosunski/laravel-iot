<?php

namespace Bosunski\LaravelIot\Mqtt\Contracts;

use BinSoul\Net\Mqtt\Connection;
use BinSoul\Net\Mqtt\Message;
use Exception;

interface MQTTHandlerInterface
{
    public function onOpen();

    public function onClose();

    public function onConnect(Connection $connection);

    public function onDisconnect(Connection $connection);

    public function onMessage(Message $message, MQTTClientInterface $client = null);

    public function onWarning(Exception $exception);

    public function onError(Exception $exception);
}
