<?php

/*
 * You can place your custom package configuration in here.
 */
return [
        'host'      => env('MQTT_HOST', '127.0.0.1'),
        'username'  => env('MQTT_USERNAME', 'guest'),
        'password'  => env('MQTT_PASSWORD', 'guest'),
        'port'      => env('MQTT_PORT', 1883),
];
