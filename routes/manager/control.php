<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/control/switch',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getSwitch($request));
    }
]);


$obRouter->get('/manager/switch_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getSwitchForm($request));
    }
]);

$obRouter->post('/manager/switch_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getSwitchCreate($request));
    }
]);

$obRouter->post('/manager/get_control_relay_change',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getControlRelayChange($request), 'application/json');
    }
]);

$obRouter->get('/manager/control/command',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getCommand($request));
    }
]);

$obRouter->get('/manager/command_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getCommandForm($request));
    }
]);

$obRouter->post('/manager/command_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getCommandCreate($request));
    }
]);

$obRouter->post('/manager/get_control_temperature_change',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Control::getControlTemperatureChange($request), 'application/json');
    }
]);