<?php

use \App\Http\Response;
use \App\Controller\Manager;

//$obRouter->get('/manager/inquiry',[
//    'middlewares' => [
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Inquiry::getInquiry($request));
//    }
//]);
//
//$obRouter->post('/manager/inquiry/getMyChart',[
//    'middlewares' => [
//        'api',
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Inquiry::getMyChart($request), 'application/json');
//    }
//]);

//$obRouter->post('/manager/inquiry',[
//    'middlewares' => [
//        'required-manager-login'
//    ],
//    function($request) {
//        return new Response(200, Manager\Inquiry::postInquiry($request));
//    }
//]);

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

$obRouter->get('/manager/table_search',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getTableSearch($request));
    }
]);

$obRouter->get('/manager/table_excel_download',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getTableExcelDownload($request));
    }
]);

$obRouter->post('/manager/chart_search',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Inquiry::getChartSearch($request), 'application/json');
    }
]);