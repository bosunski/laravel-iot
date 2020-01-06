<?php

/*
 * Configuration file for Laravel MQTT package
 *
 * You can place your custom package configuration in here.
 */
return [
    /**
     * This part includes the details that let's the package connects to the MQTT broker
     * successfully. If you need a sample Broker, be sure to check https://test.mosquitto.org
     */
    'host'      => env('MQTT_HOST', 'test.mosquitto.org'),
    'port'      => env('MQTT_PORT', 1883),
    'username'  => env('MQTT_USERNAME', ''),
    'password'  => env('MQTT_PASSWORD', ''),

    /**
     * This allows you to choose between subscribing to ALL Topics or the ones
     * DEFINED inside the topics.php file.
     *
     * Available options are: defined, all
     */
    'subscription' => 'defined',
];
