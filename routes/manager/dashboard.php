<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/dashboard',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getDashboard($request));
    }
]);

$obRouter->get('/manager/dashboard/table/{idx}',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Dashboard::getDashboardTable($request, $idx));
    }
]);

$obRouter->get('/manager/dashboard/chart/{idx}',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Dashboard::getDashboardChart($request, $idx));
    }
]);

$obRouter->post('/manager/dashboard/widgetNameChange',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::widgetNameChange($request), 'application/json');
    }
]);

$obRouter->post('/manager/dashboard/getWidgetItems',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getWidgetItems($request), 'application/json');
    }
]);

$obRouter->post('/manager/dashboard/getChart',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getChart($request), 'application/json');
    }
]);


//$obRouter->post('/manager/dashboard/widgetadd',[
//    'middlewares' => [
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Dashboard::setWidgetAdd($request));
//    }
//]);

//$obRouter->get('/manager/dashboard/widgetremove/{idx}',[
//    'middlewares' => [
//        'required-manager-login'
//    ],
//    function($request, $idx) {
//        return new Response(200, Manager\Dashboard::setWidgetRemove($request, $idx));
//    }
//]);

//$obRouter->post('/manager/dashboard/get_widget_board',[
//    'middlewares' => [
//        'api',
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Dashboard::getWidgetBoard($request), 'application/json');
//    }
//]);

//$obRouter->post('/manager/dashboard/getMyChart',[
//    'middlewares' => [
//        'api',
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Dashboard::getMyChart($request), 'application/json');
//    }
//]);


$obRouter->post('/manager/dashboard/set_push_id',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::setPushId($request), 'application/json');
    }
]);

$obRouter->post('/manager/dashboard/testChart',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Dashboard::getTestChart($request), 'application/json');
    }
]);
