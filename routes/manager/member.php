<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/member_join',[
    'middlewares' => [
        'required-manager-logout'
    ],
    function($request) {
        return new Response(200, Manager\Member::Member_Join($request));
    }
]);

$obRouter->post('/manager/member_create',[
    'middlewares' => [
        'required-manager-logout'
    ],
    function($request) {
        return new Response(200, Manager\Member::Member_Create($request));
    }
]);
