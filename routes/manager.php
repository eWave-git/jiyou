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

include __DIR__.'/manager/dashboard.php';

include __DIR__.'/manager/inquiry.php';

include __DIR__.'/manager/management.php';

include __DIR__ . '/manager/alarm.php';

include __DIR__ . '/manager/control.php';

include __DIR__.'/manager/etc.php';

include __DIR__.'/manager/member.php';

include __DIR__.'/manager/login.php';
