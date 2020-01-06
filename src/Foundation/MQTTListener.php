<?php

namespace Xeviant\LaravelIot\Foundation;

use BinSoul\Net\Mqtt\Connection;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTHandlerInterface;
use BinSoul\Net\Mqtt\DefaultConnection;
use BinSoul\Net\Mqtt\DefaultSubscription;
use BinSoul\Net\Mqtt\Message;
use BinSoul\Net\Mqtt\Subscription;
use Closure;
use Exception;
use React\EventLoop\LoopInterface;

class MQTTListener
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

    /**
     * @var MqttRouter
     */
    private $router;

    public function __construct(MQTTClientInterface $client, MQTTHandlerInterface $handler, MqttRouter $router)
    {
        $this->client = $client;
        $this->handler = $handler;
        $this->loop = app(LoopInterface::class);
        $this->router = $router;
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
            $this->handler->onMessage($message, $this->client);

            $this->stopListenerIfNecessary();
        });
    }

    public function stopListenerIfNecessary()
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
        $this->client->disconnect();
        exit($status);
    }

    /**
     * Determine if the listener should restart.
     *
     * @return bool
     */
    protected function serverShouldRestart()
    {
        return $this->getTimestampOfLastServerRestart() != $this->lastRestart;
    }

    /**
     * Get the last listener restart timestamp, or null.
     *
     * @return int|null
     */
    protected function getTimestampOfLastServerRestart()
    {
        if ($cache = app()['cache']) {
            return $cache->get('xeviant:mqtt:restart');
        }

        return null;
    }

    /**
     * Puts the MQTT Server in Listening Mode
     */
    public function listen()
    {
        $this->registerHandlers();

        $connectionPromise = $this->client->connect(
            config('mqtt.host'),
            config('mqtt.port'),
            $this->createDefaultConnection()
        );

        $connectionPromise->then(
            function () {
                if (config('mqtt.subscription', 'defined') === 'all') {
                    return $this->subscribeToAllTopics();
                }

                return $this->subscribeToDefinedTopics();
            });

        $this->lastRestart = $this->getTimestampOfLastServerRestart();

        return $connectionPromise;
    }

    protected function createDefaultConnection(): Connection
    {
        return new DefaultConnection(config('mqtt.username'), config('mqtt.password'));
    }

    protected function subscribeToDefinedTopics()
    {
        foreach ($this->router->getTopics() as $topic) {
            $this->client->subscribe(new DefaultSubscription($topic['route']))
                ->then(function (Subscription $subscription) {
                    echo sprintf("Subscribe To: %s\n", $subscription->getFilter());
                })->otherwise(function (Exception $e) {
                    echo sprintf("Subscription Error: %s\n", $e->getMessage());
                });
        }

        return true;
    }

    protected function subscribeToAllTopics()
    {
        $this->client->subscribe(new DefaultSubscription('#'))
            ->then(function (Subscription $subscription) {
                echo sprintf("Subscribe: %s\n", $subscription->getFilter());
            })->otherwise(function (Exception $e) {
                echo sprintf("Error: %s\n", $e->getMessage());
            });

        return true;
    }
}
