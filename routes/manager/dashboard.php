<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager',[
//    'middlewares' => [
//        'required-admin-login'
//    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getDashboard($request));
    }
]);
