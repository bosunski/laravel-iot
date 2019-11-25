<?php

use Xeviant\LaravelIot\Foundation\MqttRouter;

$mqtt = app()->make(MqttRouter::class);

$mqtt->topic('/state/{id}', function ($id, $payload) {
    echo($payload);
});

$mqtt->topic('/values/{id}', 'ValuesController@updateValues');

$mqtt->topic('/deploy/:id', function ($id, $payload) {
    echo($payload);
});
