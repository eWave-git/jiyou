<?php

use \App\Http\Response;
use \App\Controller\Admin;
use \app\Utils\Common;

$obRouter->get('/admin/farm_list',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Farm::Farm_List($request));
    }
]);

$obRouter->get('/admin/farm_form',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Farm::Farm_Form($request));
    }
]);

$obRouter->post('/admin/farm_form/create',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Farm::Farm_Create($request));
    }
]);

$obRouter->get('/admin/farm_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Farm::Farm_Form($request, $idx));
    }
]);

$obRouter->post('/admin/farm_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Farm::Farm_Edit($request, $idx));
    }
]);

$obRouter->get('/admin/farm_form/{idx}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Farm::Farm_Delete($request, $idx));
    }
]);

$obRouter->post('/admin/farm_form/addAddres',[
    'middlewares' => [
        'api',
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Farm::Farm_Address_Add($request), 'application/json');
    }
]);