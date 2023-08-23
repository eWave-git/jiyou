<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/setting',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Setting::getSetting($request));
    }
]);

$obRouter->get('/manager/setting_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Setting::Setting_Form($request));
    }
]);

$obRouter->post('/manager/setting_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Setting::Setting_Create($request));
    }
]);

$obRouter->get('/manager/setting_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Setting::Setting_Form($request, $idx));
    }
]);

$obRouter->post('/manager/setting_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Setting::Setting_Edit($request, $idx));
    }
]);