<?php

namespace Xeviant\LaravelIot\Mqtt\Controllers;



use App\Device;
use App\Events\BoomEvent;
use App\Events\DeviceDataReceived;
use App\Value;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;

class ValuesController
{
    /**
     * @var Value
     */
    private $value;
    /**
     * @var Device
     */
    private $device;

    /**
     * @var mixed|ConnectionInterface
     */
    private $connection;

    private $payload;

    public function __construct(Value $value, Device $device)
    {
        $this->value = $value;
        $this->device = $device;
        $this->connection = app()->get('react.db.connection');
    }

    public function updateValues($id, $payload)
    {

//        $this->payload = json_decode($payload);

        event(new DeviceDataReceived($id, $payload));

//        $this->connection->query("SELECT * FROM devices where unique_id='$id' LIMIT 1")
//            ->then(function(QueryResult $result) use ($id) {
//                $this->updateDeviceValues($id, $result);
//            },
//            function (\Exception $error) {
//                echo 'Error: ' . $error->getMessage() . PHP_EOL;
//            }
//        );
    }

    /**
     * @param string $id
     * @param QueryResult $result
     */
    protected function updateDeviceValues(string $id, QueryResult $result)
    {
//        exit(event(new DeviceDataReceived($id)));

//        $device = (object) $result->resultRows[0];
//        $sql = "UPDATE `values` SET";
//
//        foreach ($this->payload as $key => $value) {
//            $sql .= " $key = '$value'";
//            if (last($this->payload) !== $value) $sql .= ', ';
//        }
//
//        $sql .= " WHERE device_id=$device->id";
//
//        $this->connection->query($sql)->then(function (QueryResult $result) {
//            dump($result);
//        })->then(function() use ($id) {
//            event(new DeviceDataReceived($id, $this->payload));
//        });
    }
}
