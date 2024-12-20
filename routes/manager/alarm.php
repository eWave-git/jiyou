<?php

use \App\Http\Response;
use \App\Controller\Manager;

$obRouter->get('/manager/alarm_list',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getAlarm($request));
    }
]);

$obRouter->get('/manager/alarm_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::Alarm_Form($request));
    }
]);

$obRouter->post('/manager/alarm_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::Alarm_Create($request));
    }
]);

$obRouter->get('/manager/alarm_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Alarm::Alarm_Form($request, $idx));
    }
]);

$obRouter->post('/manager/alarm_form/{idx}/edit',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request, $idx) {
        return new Response(200, Manager\Alarm::Alarm_Edit($request, $idx));
    }
]);

$obRouter->get('/manager/alarm_log_list',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::AlarmLogList($request));
    }
]);

$obRouter->post('/manager/alarm_form/getBoardType',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getBoardType($request), 'application/json');
    }
]);

$obRouter->post('/manager/alarm/setActiveChange',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::setActiveChange($request), 'application/json');
    }
]);

$obRouter->get('/manager/alarm/{idx}/delete',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Alarm::AlarmDelete($request, $idx));
    }
]);

$obRouter->get('/manager/water_alarm_list',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getWaterAlarm($request));
    }
]);

$obRouter->get('/manager/water_alarm_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::Water_Alarm_Form($request));
    }
]);

$obRouter->get('/manager/water_alarm_log_list', [
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::WaterAlarmLogList($request));
    }
]);

$obRouter->post('/manager/water_alarm_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::Water_Alarm_Create($request));
    }
]);

$obRouter->post('/manager/alarm/setWaterActiveChange',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::setWaterActiveChange($request), 'application/json');
    }
]);

$obRouter->get('/manager/alarm/{idx}/water_delete',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Alarm::WaterAlarmDelete($request, $idx));
    }
]);

$obRouter->post('/manager/alarm_form/getWaterBoardType',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getWaterBoardType($request), 'application/json');
    }
]);

$obRouter->get('/manager/group_alarm_list',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getGroupAlarm($request));
    }
]);

$obRouter->get('/manager/group_alarm_form',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getGroupAlarm_Form($request));
    }
]);

$obRouter->post('/manager/group_alarm_form_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getGroupAlarm_Create($request));
    }
]);

$obRouter->get('/manager/group_alarm_detail/{idx}',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Alarm::getGroupAlarm_detail($request, $idx));
    }
]);

$obRouter->post('/manager/group_alarm_form_add_create',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::getGroupAlarmAdd_Create($request));
    }
]);

$obRouter->get('/manager/group_alarm/{idx}/delete',[
    'middlewares' => [
        'required-manager-login'
    ],
    function($request,$idx) {
        return new Response(200, Manager\Alarm::GroupAlarmDelete($request, $idx));
    }
]);

$obRouter->post('/manager/group_alarm/setGroupActiveChange',[
    'middlewares' => [
        'api',
        'required-manager-login'
    ],
    function($request) {
        return new Response(200, Manager\Alarm::setGroupActiveChange($request), 'application/json');
    }
]);

//