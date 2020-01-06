<?php

use Xeviant\LaravelIot\Facade\Mqtt;

Mqtt::subscribe('/hello', function ($payload) {
    echo($payload);
});

Mqtt::subscribe('/values/{id}', 'ValuesController@updateValues');
