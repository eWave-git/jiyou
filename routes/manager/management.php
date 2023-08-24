<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/managment',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::getManagement($request));
    }
]);

$obRouter->get('/manager/management_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::Management_Form($request));
    }
]);


$obRouter->post('/manager/management_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::Management_Create($request));
    }
]);

$obRouter->post('/manager/management/get_control_type',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::getControl($request), 'application/json');
    }
]);

$obRouter->post('/manager/management/get_control_relay_change',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::getControlRelayChange($request), 'application/json');
    }
]);

$obRouter->post('/manager/management/get_control_temperature_change',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Management::getControlTemperatureChange($request), 'application/json');
    }
]);