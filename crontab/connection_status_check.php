<?php
// sms Group send test file 추후 삭제해도 무관
include_once __DIR__."/crontab_init.php";

use App\Model\Entity\AlarmControl as EntityAlarmControl;
use App\Model\Entity\WidgetConnectionTime as EntityWidgetConnectionTime;
use \App\Utils\Common;
use App\Model\Entity\Member as EntityMmeber;

$longopt = array(
    'member_id::'
);
$param = getopt('', $longopt);

if (empty($param)) {
    echo "memeber_id 값을 입력 하세요.";
    exit;
}

$member_idx = $param['member_id'];

$member_info = Common::get_member_info($member_idx);
$results_activation = Common::getAlarmcontrolActivation($member_info['member_group']);

if ($results_activation == 'Y') {
    exit;
}

$results = EntityWidgetConnectionTime::getWidgetConnectionByMemberIdx($member_idx);

$widget_arr = array();
while ($widget_obj = $results->fetchObject(EntityWidgetConnectionTime::class)) {
    $result = Common::widgetConnectionCheck($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $widget_obj->check_time);

    if ($result == false) {
        $farm_name = $widget_obj->farm_name;
        $widget_arr[] = $widget_obj->widget_name." - 송수신 점검";
    }
}

if (!empty($widget_arr)) {
    $text = implode(", \n", $widget_arr);
    $body = "<"."데이터점검".">\n [".$farm_name."]\n".$text;

    $member_group_results = EntityMmeber::getMemberByGroup($member_idx);
    while ($member_obj = $member_group_results->fetchObject(EntityMmeber::class)) {
        if (!empty($member_obj->member_phone)) {
            $member_phone = str_replace('-','',  $member_obj->member_phone);
            Common::aligoSendSms("장치 경보 발생", $body,$member_phone);
        }
        if (!empty($member_obj->push_subscription_id)) {
            Common::sendPush("경보", $body, $member_obj->push_subscription_id, "");
        }

    }
}

