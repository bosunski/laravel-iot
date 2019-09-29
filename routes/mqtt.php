<?php

use App\Foundation\Mqtt;

$mqtt = app()->make(Mqtt::class);

$mqtt->topic('/state/{id}', function ($id, $payload) {
    echo($payload);
});

$mqtt->topic('/values/{id}', 'ValuesController@updateValues');

$mqtt->topic('/deploy/:id', function ($id, $payload) {
    echo($payload);
});
