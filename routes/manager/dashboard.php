<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/dashboard',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getDashboard($request));
    }
]);

$obRouter->post('/manager/dashboard/get_widget_board',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getWidgetBoard($request), 'application/json');
    }
]);

$obRouter->post('/manager/dashboard/testChart',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getTestChart($request), 'application/json');
    }
]);
