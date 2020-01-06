<?php

namespace Xeviant\LaravelIot\Foundation;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Xeviant\LaravelIot\Helpers\Console;
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
     * @var Console
     */
    private $console;

    /**
     * MqttHandler constructor.
     * @param MQTTClientInterface $client
     */
    public function __construct(MQTTClientInterface $client)
    {
        $this->mqttRouter = app(MqttRouter::class);
        $this->console = app(Console::class);
        $this->client = $client;
    }

    public function onOpen()
    {
        $this->console->write(
            sprintf("📡 MQTT Connection Opened: %s:%s\n", $this->client->getHost(), $this->client->getPort())
        );
    }

    public function onClose()
    {
        $this->console->write(
            sprintf("MQTT Connection Closed: %s:%s\n", $this->client->getHost(), $this->client->getPort())
        );

        App::make(LoopInterface::class)->stop();
    }

    public function onConnect(Connection $connection)
    {
        $this->console->write(
            sprintf("Client *%s* Connected:\n", $connection->getClientID())
        );
    }

    public function onDisconnect(Connection $connection)
    {
        $this->console->write(
            sprintf("Client *%s* Disconnected\n", $connection->getClientID())
        );
    }

    /**
     * @param Message $message
     * @param MQTTClientInterface|null $client
     * @throws Exception
     */
    public function onMessage(Message $message, MQTTClientInterface $client = null)
    {
        $this->console->write('📩 Received Message');

        if ($message->isDuplicate()) {
            $this->console->write( ' [duplicated]');
            return;
        }

        if ($message->isRetained()) {
            $this->console->write(' [retained]');
        }

        $this->console->writeln(': '.$message->getTopic().' => ' . mb_strimwidth($message->getPayload(), 0, 50, '...'));

        try {
            $this->mqttRouter->dispatchToRoute($message->getTopic(), $message->getPayload());
        } catch (ResourceNotFoundException $exception) {
            $this->console->writeln($exception->getMessage());
        }
    }

    public function onWarning(Exception $exception)
    {
        $this->console->write(sprintf("Warning: %s\n", $exception->getMessage()));
    }

    public function onError(Exception $exception)
    {
        $this->console->write(sprintf("Error: %s\n", $exception->getMessage()));

        App::make(LoopInterface::class)->stop();
    }
}
