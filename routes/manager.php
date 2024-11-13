<?php
use \App\Http\Response;
use \App\Controller\Manager;


$obRouter->get('/',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        $request->getRouter()->redirect('/manager/dashboard');

        exit;
    }
]);

$obRouter->get('/manager',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        $request->getRouter()->redirect('/manager/dashboard');

        exit;
    }
]);

$obRouter->get('/manager/',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        $request->getRouter()->redirect('/manager/dashboard');

        exit;
    }
]);

// test를 위한 페이지
$obRouter->get('/manager/test',[
    'middlewares' => [
    ],
    function($request) {
        return new Response(200, Manager\Test::index($request));
    }
]);

$obRouter->post('/manager/test_back',[
    'middlewares' => [
        'api',
    ],
    function($request) {
        return new Response(200, Manager\Test::index_back($request), 'application/json');
    }
]);

$obRouter->get('/manager/test_sms',[
    'middlewares' => [
    ],
    function($request) {
        return new Response(200, Manager\Test::sms($request));
    }
]);



include __DIR__.'/manager/dashboard.php';

include __DIR__.'/manager/inquiry.php';

include __DIR__.'/manager/management.php';

include __DIR__ . '/manager/alarm.php';

include __DIR__ . '/manager/control.php';

include __DIR__.'/manager/etc.php';

include __DIR__.'/manager/member.php';

include __DIR__.'/manager/login.php';

