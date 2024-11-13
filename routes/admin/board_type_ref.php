<?php

use \App\Http\Response;
use \App\Controller\Admin;

$obRouter->get('/admin/board_type_ref_list',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_List($request));
    }
]);

$obRouter->get('/admin/board_type_ref_form',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_Form($request));
    }
]);

$obRouter->post('/admin/board_type_ref_form/create',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {

        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_Create($request));
    }
]);

$obRouter->get('/admin/board_type_ref_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_Form($request, $idx));
    }
]);

$obRouter->post('/admin/board_type_ref_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_Edit($request, $idx));
    }
]);

$obRouter->get('/admin/board_type_ref_form/{idx}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\BoardTypeRef::BoardTypeRef_Delete($request, $idx));
    }
]);
