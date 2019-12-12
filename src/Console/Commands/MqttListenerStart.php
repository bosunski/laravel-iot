<?php

namespace Xeviant\LaravelIot\Console\Commands;

use Illuminate\Console\Command;
use React\EventLoop\LoopInterface;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        resolve('xeviant.mqtt.listener')->listen()->then(function ($started) {
            echo "🚀 #Listener Started", PHP_EOL;
        });

        app(LoopInterface::class)->run();
    }
}
