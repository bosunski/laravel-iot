<?php

namespace Xeviant\LaravelIot\Console\Commands;

use Xeviant\LaravelIot\Foundation\MQTTListener;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

class MqttListenerStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts MQTT Listener';

    /**
     * @var Application|MQTTListener
     */
    private $mqttListener;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mqttListener = app('xeviant.mqtt.listener');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->mqttListener->listen();
    }
}
