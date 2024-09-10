<?php
include_once __DIR__."/crontab_init.php";

use App\Model\Entity\WidgetConnectionTime as EntityWidgetConnectionTime;
use \App\Utils\Common;

$results = EntityWidgetConnectionTime::getWidgetConnections();

while ($widget_obj = $results->fetchObject(EntityWidgetConnectionTime::class)) {
    $result = Common::widgetConnectionCheck($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $widget_obj->check_time);

    if ($result == false) {
        $body = $widget_obj->widget_name." 장치 경보 발생";
        $member_phone = str_replace('-','',  $widget_obj->member_phone); ;
        Common::aligoSendSms("장치 경보 발생", $body,$member_phone);
    }
}
