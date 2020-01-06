<?php

use Xeviant\LaravelIot\Facade\Mqtt;

Mqtt::topic('/hello', function ($payload) {
    echo($payload);
});

Mqtt::topic('/values/{id}', 'ValuesController@updateValues');

Mqtt::topic('/deploy/:id', function ($id, $payload) {
    echo($payload);
});
