<?php

use \App\Http\Response;
use \App\Controller\Admin;

$obRouter->get('/admin/device_list',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Device::Device_List($request));
    }
]);

$obRouter->get('/admin/device_form',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Device::Device_Form($request));
    }
]);

$obRouter->post('/admin/device_form/create',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Device::Device_Create($request));
    }
]);

$obRouter->get('/admin/device_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Device::Device_Form($request, $idx));
    }
]);

$obRouter->post('/admin/device_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Device::Device_Edit($request, $idx));
    }
]);

$obRouter->get('/admin/device_form/{idx}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Device::Device_Delete($request, $idx));
    }
]);

$obRouter->post('/admin/device_form/searchAddres',[
    'middlewares' => [
        'api',
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Device::search_Addres($request), 'application/json');
    }
]);