<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'host'      => env('MQTT_HOST', 'test.mosquitto.org'),
    'port'      => env('MQTT_PORT', 1883),
    'username'  => env('MQTT_USERNAME', ''),
    'password'  => env('MQTT_PASSWORD', ''),
];
