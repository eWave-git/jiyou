<?php
include_once __DIR__."/crontab_init.php";
use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\GroupAlarm as EntityGroupAlarm;
use \App\Model\Entity\Alarm as EntityAlarm;

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

$member_idx = $param['member_id'];
$group_results = (new Database('group_alarm'))->execute("
                select * from group_alarm 
                where member_idx = ".$member_idx." and activation = 'Y'");
$group_array = array();
$_i = 0;

while ($group_obj = $group_results->fetchObject(EntityGroupAlarm::class)) {
    $group_array[$_i]['idx'] = $group_obj->idx;
    $group_array[$_i]['member_idx'] = $group_obj->member_idx;
    $group_array[$_i]['group_name'] = $group_obj->group_name;
    $group_array[$_i]['activation'] = $group_obj->activation;

    $_i++;
};

$alarm_array = array();
$alarm_history_array = array();
$_i = 0;
foreach ($group_array as $k => $v) {
    $alarm_results = (new Database('alarm'))->execute("
                    select * from alarm as a 
                        left join widget as w
                        on a.device_idx = w.device_idx
                    where a.member_idx = ".$member_idx." and a.group_idx = ".$v['idx']."
                     ");

    while ($alarm_obj = $alarm_results->fetchObject(EntityAlarm::class)) {
        $raw_check = Common::alarm_validity_check($alarm_obj);
        if ($raw_check['reslut']) {

            $alarm_array[] = $alarm_obj->widget_name."-".$alarm_obj->board_type_name." ".$raw_check['raw_data_value'].$raw_check['range_value'];

            $alarm_history_array[$_i]['alarm_idx'] = $alarm_obj->idx;
            $alarm_history_array[$_i]['device_idx'] = $alarm_obj->device_idx;
            $alarm_history_array[$_i]['board_type_field'] = $alarm_obj->board_type_field;
            $alarm_history_array[$_i]['board_type_name'] = $alarm_obj->board_type_name;

            $alarm_history_array[$_i]['min'] = $alarm_obj->min;
            $alarm_history_array[$_i]['max'] = $alarm_obj->max;
            $alarm_history_array[$_i]['raw_data_idx'] = $raw_check['raw_data_idx'];
            $alarm_history_array[$_i]['raw_data_value'] = $raw_check['raw_data_value'];
            $alarm_history_array[$_i]['raw_data_created_at'] = $raw_check['raw_data_created_at'];

            $_i++;
        }
    }
}

$widget_names = implode(", \n", $alarm_array);
$farm_obj = EntityMmeber::getMembersFarm($member_idx)->fetchObject(EntityMmeber::class);
$farm_name = $farm_obj->farm_name;

$alarm_contents = "<알람>\n [".$farm_name."]\n".$widget_names;

if (!empty($alarm_array)) {
    $member_results = EntityMmeber::getMemberByGroup($member_idx);

    while ($member_obj = $member_results->fetchObject(EntityMmeber::class)) {
        if (!empty($member_obj->member_phone)) {
            $member_phone = str_replace('-','', $member_obj->member_phone); ;
            Common::aligoSendSms("경보", $alarm_contents, $member_phone);
        }
        if (!empty($member_obj->push_subscription_id)) {
            Common::sendPush("경보", $alarm_contents, $member_obj->push_subscription_id, "");
        }
    }

    Common::alarmHistoryInsert($member_idx, $alarm_contents, $alarm_history_array);

}