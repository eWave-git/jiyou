<?php


$obRouter->get('/admin',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        $request->getRouter()->redirect('/admin/member_list');

        exit;
    }
]);

$obRouter->get('/admin/',[
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        $request->getRouter()->redirect('/admin/member_list');

        exit;
    }
]);

include __DIR__.'/admin/login.php';

include __DIR__.'/admin/farm.php';

include __DIR__.'/admin/member.php';

include __DIR__.'/admin/device.php';

include __DIR__.'/admin/board_type_ref.php';

include __DIR__.'/admin/all_page.php';