<?php

namespace Xeviant\LaravelIot\Console\Commands;

use Xeviant\LaravelIot\Foundation\MQTTListener;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

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
    private $mqttServer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mqttServer = app('xeviant.mqtt.listener');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $topics = app('mqtt.router')->geTopics();
    }
}
