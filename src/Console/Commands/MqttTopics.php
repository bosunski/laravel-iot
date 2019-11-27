<?php

namespace Xeviant\LaravelIot\Console\Commands;

use Xeviant\LaravelIot\Foundation\MQTTListener;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Xeviant\LaravelIot\Foundation\MqttRouter;

class MqttTopics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:topics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows MQTT Topics registered for listening';

    /**
     * @var Application|MQTTListener
     */
    private $MQTTListener;

    private $headers = ['Topic', 'Handler'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->MQTTListener = app('xeviant.mqtt.listener');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * @var $router MqttRouter
         */
        $router = app('mqtt.router');

        $topics = $router->getTopics();

        $this->table($this->headers, collect($topics)->map(function ($topic) {
            return $this->getTopicDetails($topic);
        }));
    }

    protected function getTopicDetails($topic): array
    {
        if (is_callable($topic['handler'])) {
            $topic['handler'] = 'Closure';
        }

        return $topic;
    }
}
