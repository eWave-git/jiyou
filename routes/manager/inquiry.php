<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/inquiry',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getInquiry($request));
    }
]);

$obRouter->get('/manager/chart_inquiry',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getChartInquiry($request));
    }
]);

$obRouter->get('/manager/table_inquiry',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getTableInquiry($request));
    }
]);