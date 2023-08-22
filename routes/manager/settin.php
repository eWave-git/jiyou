<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/settin',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Settin::getSettin($request));
    }
]);

$obRouter->get('/manager/settin_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Settin::Settin_Form($request));
    }
]);

$obRouter->post('/manager/settin_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Settin::Settin_Create($request));
    }
]);

$obRouter->get('/manager/settin_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Settin::Settin_Form($request, $idx));
    }
]);

$obRouter->post('/manager/settin_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Settin::Settin_Edit($request, $idx));
    }
]);