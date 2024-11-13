<?php

use \App\Http\Response;
use \App\Controller\Admin;

$obRouter->get('/admin/member_list',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Member::Member_List($request));
    }
]);

$obRouter->get('/admin/member_form',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Member::Member_Form($request));
    }
]);

$obRouter->post('/admin/member_form/create',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Member::Member_Create($request));
    }
]);

$obRouter->get('/admin/member_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Member::Member_Form($request, $idx));
    }
]);

$obRouter->post('/admin/member_form/{idx}/edit',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Member::Member_Edit($request, $idx));
    }
]);

$obRouter->get('/admin/member_form/{idx}/delete',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\Member::Member_Delete($request, $idx));
    }
]);

$obRouter->post('/admin/member/idCheck',[
    'middlewares' => [
        'api',
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Member::id_Check($request), 'application/json');
    }
]);