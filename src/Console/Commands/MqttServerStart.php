<?php

namespace Xeviant\LaravelIot\Console\Commands;

use Xeviant\LaravelIot\Foundation\MQTTServer;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

class MqttServerStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts MQTT Server';

    /**
     * @var Application|MQTTServer
     */
    private $mqttServer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mqttServer = app('xeviant.mqtt.server');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->mqttServer->listen();
    }
}
