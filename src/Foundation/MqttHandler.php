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
    private $mqttTopic;

    /**
     * MqttHandler constructor.
     * @param MQTTClientInterface $client
     */
    public function __construct(MQTTClientInterface $client)
    {
        $this->mqttTopic = app(MqttRouter::class);
        $this->client = $client;
    }

    public function onOpen()
    {
        echo sprintf("Open: %s:%s\n", $this->client->getHost(), $this->client->getPort());
    }

    public function onClose()
    {
        echo sprintf("Close: %s:%s\n", $this->client->getHost(), $this->client->getPort());

        App::make(LoopInterface::class)->stop();
    }

    public function onConnect(Connection $connection)
    {
        echo sprintf("Connect: client=%s\n", $connection->getClientID());
    }

    public function onDisconnect(Connection $connection)
    {
        echo sprintf("Disconnect: client=%s\n", $connection->getClientID());
    }

    /**
     * @param Message $message
     * @param MQTTClientInterface|null $client
     * @throws Exception
     */
    public function onMessage(Message $message, MQTTClientInterface $client = null)
    {
        echo 'Message';

        if ($message->isDuplicate()) {
            echo ' (duplicate)';
            return;
        }

        if ($message->isRetained()) {
//            if (app()->runningUnitTests()) {
//                return;
//            }

            echo ' (retained)';
        }

        echo ': '.$message->getTopic().' => ' . mb_strimwidth($message->getPayload(), 0, 50, '...');
        echo PHP_EOL;

        try {
            $this->mqttTopic->handle($message->getTopic(), $message->getPayload());
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
