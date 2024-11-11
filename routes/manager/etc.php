<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/etc/group',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Group::getGroup($request));
    }
]);

$obRouter->get('/manager/etc/group_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Group::Group_Form($request));
    }
]);

$obRouter->post('/manager/etc/group_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Group::Group_Create($request));
    }
]);

$obRouter->get('/manager/etc/group/{idx}/delete',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Group::Group_Delete($request, $idx));
    }
]);

$obRouter->post('/manager/etc/report_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Report::Report_Create($request));
    }
]);

$obRouter->get('/manager/etc/report_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Report::Report_Form($request));
    }
]);

$obRouter->get('/manager/etc/info/passwordChange',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::Password_Change($request));
    }
]);

$obRouter->post('/manager/etc/info/passwordChange_post',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::Password_Change_Post($request));
    }
]);

$obRouter->get('/manager/etc/jejunonghyeob',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::jejunonghyeob($request));
    }
]);

$obRouter->post('/manager/etc/ajax_jejunonghyeob',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::ajax_jejunonghyeob($request),'application/json');
    }
]);

$obRouter->get('/manager/etc/alarmcontrol',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::alarmcontrol($request));
    }
]);

$obRouter->post('/manager/etc/setAlarmcontrolChange',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::setAlarmcontrolChange($request),'application/json');
    }
]);

$obRouter->get('/manager/etc/graphic_view',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::Graphic_view($request));
    }
]);

$obRouter->post('/manager/etc/ajax_graphicview',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Etc::ajax_graphicview($request),'application/json');
    }
]);