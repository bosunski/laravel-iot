<?php

namespace Xeviant\LaravelIot\Foundation;

use function Amp\call;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use BinSoul\Net\Mqtt\DefaultConnection;
use BinSoul\Net\Mqtt\DefaultSubscription;
use BinSoul\Net\Mqtt\Message;
use BinSoul\Net\Mqtt\Subscription;
use Closure;
use Exception;
use React\EventLoop\LoopInterface;

class MQTTServer
{
    /**
     * @var MQTTClientInterface
     */
    private $client;

    /**
     * @var MQTTHandlerInterface
     */
    private $handler;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * Time the Server was last Restarted
     *
     * @var $lastRestart int
     */
    private $lastRestart;

    public function __construct(MQTTClientInterface $client, MQTTHandlerInterface $handler)
    {
        $this->client = $client;
        $this->handler = $handler;
        $this->loop = app('amp.loop');
    }

    protected function startServer()
    {
        $this->loop->run();
    }

    protected function registerHandlers()
    {
        $this->client->on('open', Closure::fromCallable([$this->handler, 'onOpen']));
        $this->client->on('close', Closure::fromCallable([$this->handler, 'onClose']));
        $this->client->on('connect', Closure::fromCallable([$this->handler, 'onConnect']));
        $this->client->on('disconnect', Closure::fromCallable([$this->handler, 'onDisconnect']));
        $this->client->on('warning', Closure::fromCallable([$this->handler, 'onWarning']));
        $this->client->on('error', Closure::fromCallable([$this->handler, 'onError']));

        $this->client->on('message', function (Message $message) {

            call(function () use ($message) {
                yield  $this->handler->onMessage($message, $this->client);
            });

            $this->stopIfNecessary();
        });
    }

    public function stopIfNecessary()
    {
        $lastRestart = $this->getTimestampOfLastServerRestart();

        if ($this->serverShouldRestart()) {
            $this->lastRestart = $lastRestart;
            $this->stop();
        }
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param  int  $status
     * @return void
     */
    public function stop($status = 0)
    {
        exit($status);
    }

    /**
     * Determine if the server should restart.
     *
     * @return bool
     */
    protected function serverShouldRestart()
    {
        return $this->getTimestampOfLastServerRestart() != $this->lastRestart;
    }

    /**
     * Get the last server restart timestamp, or null.
     *
     * @return int|null
     */
    protected function getTimestampOfLastServerRestart()
    {
        if ($cache = app()['cache']) {
            return $cache->get('xeviant:mqtt:restart');
        }
    }

    /**
     * Puts the MQTT Server in Listening Mode
     */
    public function listen()
    {
        $this->registerHandlers();
        $connection = new DefaultConnection(config('mqtt.username'), config('mqtt.password'));

        $connectionPromise = $this->client->connect(
            config('mqtt.host'),
            config('mqtt.port'),
            $connection
        );

        $connectionPromise->then(
            function () {
                $this->client->subscribe(new DefaultSubscription('#'))
                    ->then(function (Subscription $subscription) {
                        echo sprintf("Subscribe: %s\n", $subscription->getFilter());
                    })->otherwise(function (Exception $e) {
                        echo sprintf("Error: %s\n", $e->getMessage());
                    });
            });

        $this->lastRestart = $this->getTimestampOfLastServerRestart();
        $this->startServer();
    }
}
