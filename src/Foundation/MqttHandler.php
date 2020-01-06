<?php

namespace Xeviant\LaravelIot\Foundation;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use BinSoul\Net\Mqtt\Connection;
use BinSoul\Net\Mqtt\Message;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use React\EventLoop\LoopInterface;

class MqttHandler implements MQTTHandlerInterface
{
    /**
     * @var MQTTClientInterface
     */
    private $client;

    /**
     * @var Application|MqttRouter
     */
    private $mqttRouter;

    /**
     * MqttHandler constructor.
     * @param MQTTClientInterface $client
     */
    public function __construct(MQTTClientInterface $client)
    {
        $this->mqttRouter = app(MqttRouter::class);
        $this->client = $client;
    }

    public function onOpen()
    {
        echo sprintf("MQTT Connection Opened: %s:%s\n", $this->client->getHost(), $this->client->getPort());
    }

    public function onClose()
    {
        echo sprintf("MQTT Connection Closed: %s:%s\n", $this->client->getHost(), $this->client->getPort());

        App::make(LoopInterface::class)->stop();
    }

    public function onConnect(Connection $connection)
    {
        echo sprintf("Client *%s* Connected:\n", $connection->getClientID());
    }

    public function onDisconnect(Connection $connection)
    {
        echo sprintf("Client *%s* Disconnected\n", $connection->getClientID());
    }

    /**
     * @param Message $message
     * @param MQTTClientInterface|null $client
     * @throws Exception
     */
    public function onMessage(Message $message, MQTTClientInterface $client = null)
    {
        echo '📩 Message Received';

        if ($message->isDuplicate()) {
            echo ' [duplicated]';
            return;
        }

        if ($message->isRetained()) {
            echo ' [retained]';
        }

        echo ': '.$message->getTopic().' => ' . mb_strimwidth($message->getPayload(), 0, 50, '...');
        echo PHP_EOL;

        try {
            $this->mqttRouter->dispatchToRoute($message->getTopic(), $message->getPayload());
        } catch (ResourceNotFoundException $exception) {
            echo $exception->getMessage(), PHP_EOL;
        }
    }

    public function onWarning(Exception $exception)
    {
        echo sprintf("Warning: %s\n", $exception->getMessage());
    }

    public function onError(Exception $exception)
    {
        echo sprintf("Error: %s\n", $exception->getMessage());

        App::make(LoopInterface::class)->stop();
    }
}
