<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/all',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\All::getAll($request));
    }
]);
