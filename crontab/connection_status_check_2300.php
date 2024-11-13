<?php
// sms Group send test file 추후 삭제해도 무관
include_once __DIR__."/crontab_init.php";

use App\Model\Entity\WidgetConnectionTime as EntityWidgetConnectionTime;
use \App\Utils\Common;
use App\Model\Entity\Member as EntityMmeber;

$member_idx = "42";

$results = EntityWidgetConnectionTime::getWidgetConnectionByMemberIdx($member_idx);

$widget_arr = array();
while ($widget_obj = $results->fetchObject(EntityWidgetConnectionTime::class)) {
    $result = Common::widgetConnectionCheck($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $widget_obj->check_time);

    if ($result == false) {
        $farm_name = $widget_obj->farm_name;
        $widget_arr[] = $widget_obj->widget_name;
    }
}

if (!empty($widget_arr)) {
    $text = implode(", \n", $widget_arr);
    $body = "<"."오류 발생".">".$farm_name."\n\n[".$text."]\n 미수신 상태 발생";

    $member_group_results = EntityMmeber::getMemberByGroup($member_idx);
    while ($member_obj = $member_group_results->fetchObject(EntityMmeber::class)) {
        $member_phone = str_replace('-','',  $member_obj->member_phone);
        Common::aligoSendSms("장치 경보 발생", $body,$member_phone);
    }
}

