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


    private $headers = ['S/N', 'Topic', 'Handler'];


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $topics = app('mqtt.router')->getTopics();

        $this->table($this->headers, collect($topics)->map(function ($topic, $key) {
            return $this->getTopicDetails($topic, $key);
        }));
    }

    protected function getTopicDetails($topic, int $key): array
    {
        $topic = ['S/N' => ++$key] + $topic;

        if (is_callable($topic['handler'])) {
            $topic['handler'] = 'Closure';
        }

        return $topic;
    }
}
