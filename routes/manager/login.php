<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/login',[
    'middlewares' => [
        'required-manager-logout'
    ],
    function($request) {
        return new Response(200, Manager\Login::getLogin($request));
    }
]);

$obRouter->post('/manager/login',[
    'middlewares' => [
        'required-manager-logout'
    ],
    function($request) {
        return new Response(200, Manager\Login::setLogin($request));
    }
]);

$obRouter->get('/manager/logout',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Login::setLogout($request));
    }
]);