<?php
include_once __DIR__."/crontab_init.php";

use App\Model\Entity\Member as EntityMmeber;
use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;
use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Widget as EntityWidget;

$activation =  (new Database('water_alarm'))->execute(
    "select * ,a.idx as alarm_idx, f.farm_name, w.widget_name
            from water_alarm as a
                     left join water_alarm_member am on a.idx = am.water_alarm_idx
                     left join member as m on am.member_idx = m.idx
                     left join farm as f on am.member_idx = f.member_idx
                     left join widget as w on w.device_idx = a.device_idx            
            where a.idx in (select max(idx)
                            from water_alarm
                            where activation = 'Y'
                            group by device_idx, board_type_field)
            and m.push_subscription_id is not null
            ");

$array = array();
$key = 0;

while ($activation_obj = $activation->fetchObject()) {

    $device_info = EntityDevice::getDevicesByIdx($activation_obj->device_idx);
    $widget_info = EntityWidget::getWidgetByDeviceIdx($device_info->idx)->fetchObject(EntityWidget::class);
    $widget_name = $widget_info->widget_name;

    $raw_data_info = (new Database('raw_data'))->execute(
        "select sum(L) as 'water' from (
                    select
                           (max({$activation_obj->board_type_field}) - ifnull(LAG(max({$activation_obj->board_type_field})) OVER (ORDER BY created_at), {$activation_obj->board_type_field})) * 1 as 'L'
                    from raw_data
                    where address = '{$device_info->address}'
                      and board_type = '{$device_info->board_type}'
                      and board_number = '{$device_info->board_number}'
                      and created_at > (now() - INTERVAL 1 HOUR ) and created_at < now()
                    group by DAY(date_format(created_at, '%Y-%m-%d %H:%i:00')), FLOOR(HOUR(date_format(created_at, '%Y-%m-%d %H:%i:00')) / 1) * 10
                    order BY idx asc
                ) as Temp")->fetchObject();

    if ($activation_obj->alarm_range == "between") {
        if ($activation_obj->min > $raw_data_info->water || $activation_obj->max < $raw_data_info->water  ) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['water_alarm_idx'] = $activation_obj->water_alarm_idx;
            $_txt = "[".$activation_obj->farm_name."-".$activation_obj->widget_name ."] 설정 ".$activation_obj->board_type_name." ".$activation_obj->min."~".$activation_obj->max." 범위 초과 <알람 발생> 알람 현재 ".$raw_data_info->water;
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

//            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
//            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
//            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }

    } else if ($activation_obj->alarm_range == "up") {
        if ($activation_obj->max < $raw_data_info->water ) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['water_alarm_idx'] = $activation_obj->water_alarm_idx;
            $_txt = "[".$activation_obj->farm_name."-".$activation_obj->widget_name ."] 설정 ".$activation_obj->board_type_name." ".$activation_obj->max." 이상 <알람 발생> 현재 ".$raw_data_info->water;
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

//            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
//            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
//            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }
    } else if ($activation_obj->alarm_range == "down") {
        if ($activation_obj->min > $raw_data_info->water) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['water_alarm_idx'] = $activation_obj->water_alarm_idx;
            $_txt = "[".$activation_obj->farm_name."-".$activation_obj->widget_name ."] 설정 ".$activation_obj->board_type_name." ".$activation_obj->min." 이하 <알람 발생> 현재 ".$raw_data_info->water;
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

//            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
//            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
//            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }
    }

    $key++;
}

$wateralarmHistoryDatabases = new Database('water_alarm_history');

foreach ($array as $k => $v) {

    $results  = $wateralarmHistoryDatabases->select("water_alarm_idx = '{$v['water_alarm_idx']}'","created_at desc")->fetchObject();

    if (isset($results->water_alarm_idx)) {
        // "있다면";

        /*
        $diff = Common::date_diff($results->created_at, date("Y-m-d H:i:s"), 'i');                                          // 밑 뒤에 파라미터 값이 i이면 분, h이면 시간 d이면 날짜 마다 보냄
        if ($diff >= 1) {
           alarmHistoryInsert($v);
           Common::sendPush($v['board_type_name']." 경보발생", $v['alarm_contents'],$v['push_subscription_id'],"");
        }
        */

        $diff_sec = Common::date_diff($results->created_at, date("Y-m-d H:i:s"), 's');                                          // 시간 설정 변경
        $diff_min = Common::date_diff($results->created_at, date("Y-m-d H:i:s"), 'i');
        if ($diff_sec >= 59 || $diff_min >= 0) {

            $results = EntityMmeber::getMemberByGroup($v['member_idx']);

            while ($obj = $results->fetchObject(EntityMmeber::class)) {
                if (!empty($obj->member_phone)) {
                    $member_phone = str_replace('-','', $obj->member_phone); ;
                    Common::aligoSendSms($v['board_type_name'] . " 경보", $v['alarm_contents'], $member_phone);
                }

                wateralarmHistoryInsert($v);
                Common::sendPush($v['board_type_name'] . " 경보", $v['alarm_contents'], $obj->push_subscription_id, "");
            }
        }

    } else {
        // "없다면";

            $results = EntityMmeber::getMemberByGroup($v['member_idx']);

            while ($obj = $results->fetchObject(EntityMmeber::class)) {
                if (!empty($obj->member_phone)) {
                    $member_phone = str_replace('-','', $obj->member_phone); ;
                    Common::aligoSendSms($v['board_type_name'] . " 경보", $v['alarm_contents'], $member_phone);
                }

                wateralarmHistoryInsert($v);
                Common::sendPush($v['board_type_name'] . " 경보", $v['alarm_contents'], $obj->push_subscription_id, "");
            }
    }
}

function wateralarmHistoryInsert($v) {
    $wateralarmHistoryDatabases = new Database('water_alarm_history');

    $wateralarmHistoryDatabases->insert([
        'member_idx' => $v['member_idx'],
        'member_name' => $v['member_name'],
        'push_subscription_id' => $v['push_subscription_id'],

        'device_idx' => $v['device_idx'],
        'board_type_field' => $v['board_type_field'],
        'board_type_name' => $v['board_type_name'],

        'water_alarm_idx' => $v['water_alarm_idx'],
        'alarm_contents' => $v['alarm_contents'],
        'min' => $v['min'],
        'max' => $v['max'],

        'created_at' => date("Y-m-d H:i:s"),
    ]);
}