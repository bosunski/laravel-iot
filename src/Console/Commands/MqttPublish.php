<?php

namespace Bosunski\LaravelIot\Console\Commands;

use Bosunski\LaravelIot\Foundation\PhpMQTT;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MqttPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:publish {action} {deviceId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulates a Publishing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $deviceId = $this->argument('deviceId');
        $route = '/values/' . $deviceId;

        $mqtt = $this->getMqttClient();

        for ($i = 0; $i < 1000; $i++) {
            $data = [
                'voltage' => random_int(20, 1000),
                'current' => random_int(20, 1000),
                'power' => random_int(20, 1000),
                'energy' => random_int(20, 1000),
            ];

            $mqtt->publish($route, json_encode($data), 0);
            $mqtt->publish(str_replace('values', 'state', $route), json_encode($data), 0);

            echo "Published: " . json_encode($data), PHP_EOL;

            sleep(2);
        }

        $mqtt->close();
    }

    protected function setArgs()
    {
        global $argv;

        $argv[1] = $this->argument('action');
    }

    protected function getMqttClient()
    {
        $mqtt = new PhpMQTT(config('mqtt.host'), config('mqtt.port'), $id = Str::random());
        $mqtt->connect(true, NULL, config('mqtt.username'), config('mqtt.password'));

        return $mqtt;
    }
}
