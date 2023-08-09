<?php

use \App\Http\Response;
use \App\Controller\Admin;


$obRouter->get('/admin/all_list',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\all::all_list($request));
    }
]);

$obRouter->get('/admin/all_detail/{idx}',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $idx) {
        return new Response(200, Admin\all::all_detail($request, $idx));
    }
]);